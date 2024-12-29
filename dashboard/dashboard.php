<?php
// Menghubungkan ke database
include 'function/middleware.php';
admin_only_allowed();
include 'config/database.php';

// Query untuk mendapatkan total penjualan
$totalPenjualanQuery = "SELECT SUM(oi.quantity * p.price) AS total_penjualan 
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        JOIN orders o ON oi.order_id = o.id
                        WHERE o.status = 'delivered'"; 
$stmt = $pdo->query($totalPenjualanQuery);
$totalPenjualan = $stmt->fetch(PDO::FETCH_ASSOC)['total_penjualan'];

// Query untuk menghitung produk terjual
$totalProdukTerjualQuery = "SELECT SUM(quantity) AS total_terjual
                            FROM order_items oi
                            JOIN orders o ON oi.order_id = o.id
                            WHERE o.status = 'delivered'";
$stmt = $pdo->query($totalProdukTerjualQuery);
$totalProdukTerjual = $stmt->fetch(PDO::FETCH_ASSOC)['total_terjual'];

// Query untuk menghitung pelanggan aktif
$totalPelangganAktifQuery = "SELECT COUNT(DISTINCT user_id) AS total_pelanggan
                             FROM orders
                             WHERE status = 'delivered'";
$stmt = $pdo->query($totalPelangganAktifQuery);
$totalPelangganAktif = $stmt->fetch(PDO::FETCH_ASSOC)['total_pelanggan'];

// Query untuk menghitung pesanan baru
$totalPesananBaruQuery = "SELECT COUNT(*) AS total_pesanan
                          FROM orders
                          WHERE status = 'pending'";
$stmt = $pdo->query($totalPesananBaruQuery);
$totalPesananBaru = $stmt->fetch(PDO::FETCH_ASSOC)['total_pesanan'];

// Query untuk menghitung total produk
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

// Query untuk menghitung total pengguna
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

// Query untuk menghitung total pesanan
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

// Total penghasilan
$stmt = $pdo->query("SELECT SUM(total_amount) FROM orders");
$total_revenue = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satelit Cell Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset dan Variabel Global */
        :root {
            --primary-color: #2563eb;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --background-color: #f0f4f8;
            --text-color: #1f2937;
            --gradient: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
        }

        /* Navbar Gaya Modern */
        header {
            background: var(--gradient);
            padding: 1rem 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        header nav ul {
            display: flex;
            list-style-type: none;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }

        header nav ul li a {
            color: var(--light-color);
            text-decoration: none;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        header nav ul li a i {
            font-size: 1.1em;
        }

        header nav ul li a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        header nav ul li a.active {
            background: var(--dark-color);
        }

        /* Container Dashboard */
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
        }

        .dashboard-card h2 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .dashboard-card .card-icon {
            font-size: 3em;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }

        /* Responsif */
        @media screen and (max-width: 768px) {
            header nav ul {
                flex-direction: column;
                gap: 15px;
            }

            .dashboard-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="product.php"><i class="fas fa-box"></i> Produk</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Pengguna</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
                <li><a href="../php/login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard-container">
        <div class="dashboard-card">
            <i class="fas fa-chart-line card-icon"></i>
            <h2>Total Penjualan</h2>
            <p>Rp <?= number_format($totalPenjualan, 2, ',', '.'); ?></p>
        </div>

        <div class="dashboard-card">
            <i class="fas fa-box-open card-icon"></i>
            <h2>Produk Terjual</h2>
            <p><?= number_format($totalProdukTerjual); ?> Unit</p>
        </div>

        <div class="dashboard-card">
            <i class="fas fa-users card-icon"></i>
            <h2>Pelanggan Aktif</h2>
            <p><?= number_format($totalPelangganAktif); ?> Pelanggan</p>
        </div>

        <div class="dashboard-card">
            <i class="fas fa-shopping-cart card-icon"></i>
            <h2>Pesanan Baru</h2>
            <p><?= number_format($totalPesananBaru); ?> Pesanan</p>
        </div>

        <!-- Statistik tambahan dari kode sebelumnya -->
        <div class="dashboard-card">
            <i class="fas fa-cogs card-icon"></i>
            <h2>Total Produk</h2>
            <p><?= $total_products ?></p>
        </div>
        <div class="dashboard-card">
            <i class="fas fa-user card-icon"></i>
            <h2>Total Pengguna</h2>
            <p><?= $total_users ?></p>
        </div>
        <div class="dashboard-card">
            <i class="fas fa-box-open card-icon"></i>
            <h2>Total Pesanan</h2>
            <p><?= $total_orders ?></p>
        </div>
        <div class="dashboard-card">
            <i class="fas fa-dollar-sign card-icon"></i>
            <h2>Total Penghasilan</h2>
            <p>Rp <?= number_format($total_revenue, 2) ?></p>
        </div>
    </main>
</body>
</html>
