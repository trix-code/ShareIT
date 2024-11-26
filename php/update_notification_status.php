<?php
session_start();
include("config.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['id'];
$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($id <= 0 || !in_array($action, ['confirm', 'dismiss'])) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

$status = ($action === 'confirm') ? 'read' : 'dismissed';

$query = "UPDATE notifications SET status = '$status' WHERE id = $id AND user_id = $userId";
if (mysqli_query($con, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
}
?>
