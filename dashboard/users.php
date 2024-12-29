<?php 
require_once 'config/database.php';
include 'function/middleware.php';
admin_only_allowed();
require_once 'includes/header.php'; 

// Ambil daftar pengguna
$stmt = $pdo->query("SELECT id, name, email, phone, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    .users-management {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 15px;
        background-color: var(--light-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .users-management h1 {
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
    }

    table td a:hover {
        color: var(--primary-color);
        background-color: #f0f0f0;
    }

    /* Responsif */
    @media screen and (max-width: 768px) {
        table {
            font-size: 0.9em;
        }
    }
</style>

<div class="users-management">
    <h1>Manajemen Pengguna</h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Tanggal Bergabung</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['name'] ?></td>
                <td><?= $user['email'] ?></td>
                <td><?= $user['phone'] ?></td>
                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                <td>
                    <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>