<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['productId'])) {
    $_SESSION['product_id'] = $data['productId']; 
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No product ID received']);
}
?>
