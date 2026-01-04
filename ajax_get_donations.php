<?php
require 'db_connect.php'; // your DB connection

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$type = $_GET['type'] ?? '';
$reason = $_GET['reason'] ?? '';

$limit = 5;
$offset = ($page - 1) * $limit;

// Build WHERE clause dynamically
$where = [];
$params = [];
$types = '';

if ($type) {
    $where[] = "entry_type = ?";
    $params[] = $type === 'offers' ? 'offer' : 'request';
    $types .= 's';
}

if ($reason) {
    $where[] = "reason_id = ?";
    $params[] = $reason;
    $types .= 'i';
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Count total entries for pagination
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM vw_donation_entries $whereSQL");

if ($params) {
    $refs = [];
    foreach ($params as $k => $v) $refs[$k] = &$params[$k];
    call_user_func_array([$countStmt, 'bind_param'], array_merge([$types], $refs));
}

$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

if ($total == 0) {
    // No entries found
    echo '<p class="text-center text-gray-500 py-4">No donation entries found.</p>';
    return; // stop execution, no table or pagination needed
}

// Fetch entries
$fetchStmt = $conn->prepare("
    SELECT * FROM vw_donation_entries
    $whereSQL
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");

$params_with_limit = $params;
$params_with_limit[] = $limit;
$params_with_limit[] = $offset;

$types_with_limit = $types . 'ii';
$refs = [];
foreach ($params_with_limit as $k => $v) $refs[$k] = &$params_with_limit[$k];

call_user_func_array([$fetchStmt, 'bind_param'], array_merge([$types_with_limit], $refs));
$fetchStmt->execute();
$result = $fetchStmt->get_result();

// Build table HTML
echo '<div class="overflow-x-auto">';
echo '<table class="w-full border border-gray-200 rounded text-sm shadow-sm">';
echo '<thead class="bg-gray-100">';
echo '<tr class="border-b text-left">';
echo '<th class="p-2 w-10">No.</th>';
echo '<th class="p-2 w-36">Profile</th>';
echo '<th class="p-2 w-24">Type</th>';
echo '<th class="p-2 w-64">Details</th>';
echo '<th class="p-2 w-36">Reason</th>';
echo '<th class="p-2 w-32">Target Area</th>';
echo '<th class="p-2 w-24">Date</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$no = $offset + 1;
while ($row = $result->fetch_assoc()) {
    $targetArea = $row['entry_type'] === 'request' ? '---' : htmlspecialchars($row['target_area']);

    // Color for type
    $typeColor = $row['entry_type'] === 'offer' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';

    echo '<tr class="border-b hover:bg-gray-50">';
    echo "<td class='p-2'>$no</td>";
    echo '<td class="p-2 font-medium text-blue-700">' . htmlspecialchars($row['profile_name']) . '</td>';
    echo "<td class='p-2 $typeColor'>" . ucfirst($row['entry_type']) . '</td>';
    echo '<td class="p-2 break-words">' . htmlspecialchars($row['details']) . '</td>';
    echo '<td class="p-2">' . htmlspecialchars($row['reason_name']) . '</td>';
    echo '<td class="p-2">' . $targetArea . '</td>';
    echo '<td class="p-2 text-gray-600">' . date("Y-m-d", strtotime($row['created_at'])) . '</td>';
    echo '</tr>';

    $no++;
}

echo '</tbody>';
echo '</table>';
echo '</div>';

// Pagination
if ($totalPages > 1) {
    echo '<div class="flex justify-center gap-2 mt-4 text-sm">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $page ? 'bg-blue-500 text-white' : 'hover:bg-gray-100';
        echo "<a href='#' class='px-3 py-1 border rounded pagination-link $active' data-page='$i'>$i</a>";
    }
    echo '</div>';
}
