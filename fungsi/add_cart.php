<?php
require_once '../php/config.php';
session_start();

// Cek apakah ID produk ada di URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Ambil informasi produk dari database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika produk ditemukan dan stok mencukupi
    if ($product && $product['stock'] > 0) {
        // Kurangi stok produk
        $new_stock = $product['stock'] - 1;
        $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$new_stock, $product_id]);

        // Tambahkan produk ke keranjang
        $stmt = $pdo->prepare("INSERT INTO cart (product_id, quantity, user_id) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->execute([$product_id, $user_id]);

        // Redirect ke halaman keranjang
        $_SESSION['success_message'] = 'Produk berhasil ditambahkan ke keranjang!';
        header("Location: ../php/cart.php");
        exit;
    } else {
        // Jika stok habis
        $_SESSION['error_message'] = 'Stok produk ini habis!';
        header("Location: ../php/produk.php?out_of_stock=1");
        exit;
    }
} else {
    // Jika ID produk tidak valid
    header("Location: ../php/produk.php");
    exit;
}
