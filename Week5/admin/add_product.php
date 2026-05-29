<?php
session_start();
require_once '../config/db.php';

$data        = json_decode(file_get_contents('php://input'), true);

$name        = trim($data['name']);
$description = trim($data['description']);
$price       = floatval($data['price']);
$stock       = intval($data['stock']);
$category_id = intval($data['category_id']);

if (empty($name) || empty($price) || empty($category_id)) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing']);
    exit;
}

$stmt = mysqli_prepare($conn, 
    "INSERT INTO products (category_id, name, description, price, stock) 
     VALUES (?, ?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, 'issdi', 
    $category_id, 
    $name, 
    $description, 
    $price, 
    $stock);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>