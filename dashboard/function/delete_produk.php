<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query untuk menghapus produk berdasarkan ID
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        // Redirect kembali ke halaman produk setelah berhasil hapus
        header("Location: produk.php?delete_success=1");
        exit;
    } else {
        echo "Gagal menghapus produk.";
    }
} else {
    echo "ID produk tidak ditemukan.";
}
?>
