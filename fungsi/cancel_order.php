<?php
// cancel_order.php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'];

// Verify that the order belongs to the current user and is still pending
$stmt = $pdo->prepare("
    SELECT id 
    FROM orders 
    WHERE id = :order_id 
    AND user_id = :user_id 
    AND status = 'pending'
");
$stmt->bindParam(':order_id', $order_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order) {
    // Update order status to cancelled
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET status = 'cancelled' 
        WHERE id = :order_id
    ");
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    
    // Restore product stock (optional)
    $stmt = $pdo->prepare("
        UPDATE products p
        JOIN order_items oi ON p.id = oi.product_id
        SET p.stock = p.stock + oi.quantity
        WHERE oi.order_id = :order_id
    ");
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
}

header("Location: order_history.php");
exit;
?>