<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../php/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'];


try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Get cart item details
    $stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity 
                          FROM cart c 
                          WHERE c.product_id = :product_id 
                          AND c.user_id = :user_id");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // Return quantity to product stock
        $updateStmt = $pdo->prepare("UPDATE products 
                                   SET stock = stock + :quantity 
                                   WHERE id = :product_id");
        $updateStmt->bindParam(':quantity', $item['quantity']);
        $updateStmt->bindParam(':product_id', $product_id);
        $updateStmt->execute();

        // Remove item from cart
        $deleteStmt = $pdo->prepare("DELETE FROM cart 
                                   WHERE id = :cart_id 
                                   AND user_id = :user_id");
        $deleteStmt->bindParam(':cart_id', $item['cart_id']);
        $deleteStmt->bindParam(':user_id', $user_id);
        $deleteStmt->execute();

        // Commit transaction
        $pdo->commit();
        
        // Set success message in session
        $_SESSION['success_message'] = "Produk berhasil dihapus dari keranjang";
    }
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    $_SESSION['error_message'] = "Gagal menghapus produk dari keranjang: " . $e->getMessage();

}

// Redirect back to products page
header("Location: ../php/produk.php");
exit;
?>
