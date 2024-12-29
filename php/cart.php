<?php
session_start();
require_once 'config.php'; // Menghubungkan ke database

// Ambil data produk yang ada di keranjang dari tabel cart
$stmt = $pdo->prepare("SELECT cart.product_id AS id, products.name, products.price, cart.quantity 
                       FROM cart 
                       INNER JOIN products ON cart.product_id = products.id WHERE cart.user_id = :user_id");
$user_id = $_SESSION['user_id'] ?? 0;
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total belanja
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="../css/cart.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container cart-container" style="padding-top: 150px; padding-bottom: 50px;">
        <h1>Keranjang Belanja</h1>
        
        <?php if (count($cart_items) > 0) { ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item) { ?>
                        <tr>
                            <td data-label="Produk" class="product-name">
                                <i class="fas fa-box me-2"></i>
                                <?php echo $item['name']; ?>
                            </td>
                            <td data-label="Harga" class="price-column">
                                <i class="fas fa-tag me-2"></i>
                                Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                            </td>
                            <td data-label="Jumlah">
                                <span class="quantity-badge">
                                    <i class="fas fa-shopping-basket me-2"></i>
                                    <?php echo $item['quantity']; ?>
                                </span>
                            </td>
                            <td data-label="Total" class="total-column">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                            </td>
                            <td data-label="Aksi">
                                <a href="../fungsi/remove_cart.php?id=<?php echo $item['id']; ?>" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Hapus
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">Total Belanja:</p>
                        <h4 class="total-amount">Rp <?php echo number_format($total_amount, 0, ',', '.'); ?></h4>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="checkout.php" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Lanjut ke Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart fa-3x mb-3" style="color: var(--gray-300);"></i>
                <p>Keranjang belanja Anda masih kosong</p>
                <a href="produk.php" class="btn btn-primary">
                    <i class="fas fa-store me-2"></i>Mulai Belanja
                </a>
            </div>
        <?php } ?>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
