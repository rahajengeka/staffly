<?php
require_once '../config/koneksi.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized session.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid employee ID.']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM employees WHERE id = :id");
$stmt->execute([':id' => $id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Employee record deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Employee record not found.']);
}
exit;
