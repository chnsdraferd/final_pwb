<?php 
include 'config/database.php';
include 'function/middleware.php';
admin_only_allowed();
require_once 'includes/header.php'; 

// Ambil daftar produk
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Menggunakan variabel dan styling dari dashboard.php */
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

    .products-management {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 15px;
        background-color: var(--light-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .products-management h1 {
        color: var(--primary-color);
        text-align: center;
        margin-bottom: 1.5rem;
        padding-top: 1rem;
    }

    .btn-add {
        display: block;
        width: 200px;
        margin: 0 auto 1.5rem;
        padding: 10px 15px;
        background: var(--gradient);
        color: var(--light-color);
        text-decoration: none;
        text-align: center;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.3s ease;
    }

    .btn-add:hover {
        transform: scale(1.05);
        background: var(--dark-color);
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 1rem;
    }

    table thead {
        background: var(--primary-color);
        color: var(--light-color);
    }

    table th, table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    table th {
        font-weight: 600;
    }

    table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    table tbody tr:hover {
        background-color: #f3f4f6;
    }

    table td a {
        text-decoration: none;
        margin: 0 10px;
        color: var(--secondary-color);
        font-weight: 500;
        transition: color 0.3s ease;
    }

    table td a:hover {
        color: var(--primary-color);
    }

    /* Responsif */
    @media screen and (max-width: 768px) {
        table {
            font-size: 0.9em;
        }
    }
</style>

<div class="products-management">
    <h1>Manajemen Produk</h1>
    <a href="/tugas_besar/function/add_produk.php" class="btn-add">Tambah Produk Baru</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Brand</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= $product['name'] ?></td>
                <td><?= $product['brand'] ?></td>
                <td>Rp <?= number_format($product['price'], 2, ',', '.') ?></td>
                <td><?= $product['stock'] ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $product['id'] ?>">Edit</a>
                    <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>