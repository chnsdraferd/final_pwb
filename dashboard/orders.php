<?php 
require_once 'config/database.php';
include 'function/middleware.php';
admin_only_allowed();
require_once 'includes/header.php'; 

// Ambil daftar pesanan dengan detail pengguna
$stmt = $pdo->query("
    SELECT o.id, o.total_amount, o.status, o.created_at, u.name as user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    .orders-management {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 15px;
        background-color: var(--light-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .orders-management h1 {
        color: var(--primary-color);
        text-align: center;
        margin-bottom: 1.5rem;
        padding-top: 1rem;
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
        color: var(--secondary-color);
        font-weight: 500;
        transition: color 0.3s ease;
        padding: 5px 10px;
        border-radius: 4px;
        margin-right: 10px;
    }

    table td a:hover {
        color: var(--primary-color);
        background-color: #f0f0f0;
    }

    /* Status styling */
    table td .status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: 600;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-delivered {
        background-color: #d1fae5;
        color: #059669;
    }

    .status-cancelled {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Responsif */
    @media screen and (max-width: 768px) {
        table {
            font-size: 0.9em;
        }
    }
</style>

<div class="orders-management">
    <h1>Manajemen Pesanan</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Pengguna</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= $order['user_name'] ?></td>
                <td>Rp <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                <td>
                    <span class="status status-<?= strtolower($order['status']) ?>">
                        <?= $order['status'] ?>
                    </span>
                </td>
                <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                <td>
                    <a href="order_detail.php?id=<?= $order['id'] ?>">Detail</a>
                    <a href="update_order_status.php?id=<?= $order['id'] ?>">Update Status</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>