<?php
require_once '../php/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'] ?? null;

// Validate order_id
if (!$order_id) {
    die("Invalid order ID");
}

// Check if status change is requested
if (isset($_GET['confirm'])) {
    try {
        // Update order status to delivered
        $stmt = $pdo->prepare("UPDATE orders SET `status` = 'delivered' WHERE id = :order_id AND `user_id` = :user_id");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Redirect with success message
        $_SESSION['payment_success'] = true;
        header("Location: payment_confirmation.php?order_id=" . $order_id);
        exit;
    } catch (Exception $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id");
$stmt->bindParam(':order_id', $order_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (isset($_SESSION['payment_success'])): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle me-2"></i> 
                        Pembayaran berhasil dikonfirmasi!
                    </div>
                    <?php unset($_SESSION['payment_success']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Konfirmasi Pembayaran</h4>
                    </div>
                    <div class="card-body text-center">
                        <h5>Order #<?php echo htmlspecialchars($order_id); ?></h5>
                        <p>Total Pembayaran: Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
                        
                        <?php if ($order['status'] === 'pending'): ?>
                            <a href="?order_id=<?php echo $order_id; ?>&confirm=1" 
                               class="btn btn-success btn-lg mt-3" 
                               onclick="return confirm('Yakin ingin mengkonfirmasi pembayaran?');">
                                <i class="fas fa-check-circle me-2"></i> 
                                Konfirmasi Pembayaran
                            </a>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Status Pesanan: <?php echo ucfirst($order['status']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>