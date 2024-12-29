<?php
// order_history.php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Retrieve all orders for the current user
$stmt = $pdo->prepare("
    SELECT o.id, o.total_amount, o.status, o.created_at,
           GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = :user_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Order History</h1>
            <a href="produk.php" class="btn btn-primary">Continue Shopping</a>
        </div>
        
        <?php if (count($orders) > 0) { ?>
            <?php foreach ($orders as $order) { ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Order #<?php echo $order['id']; ?></span>
                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                        <p><strong>Items:</strong> <?php echo $order['items']; ?></p>
                        <p><strong>Total Amount:</strong> Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
                        <?php if ($order['status'] === 'pending') { ?>
                            <a href="cancel_order.php?id=<?php echo $order['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to cancel this order?')">
                                Cancel Order
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-info">
                You haven't placed any orders yet.
            </div>
        <?php } ?>
    </div>
</body>
</html>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>