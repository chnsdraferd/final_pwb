<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Retrieve the cart items for the current user
$stmt = $pdo->prepare("SELECT c.id, p.name, p.price, c.quantity, c.product_id 
                      FROM cart c
                      JOIN products p ON c.product_id = p.id
                      WHERE c.user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Payment method handling
$payment_methods = [
    'bri' => 'Bank BRI',
    'bni' => 'Bank BNI',
    'bca' => 'Bank BCA',
    'mandiri' => 'Bank Mandiri',
    'dana' => 'DANA',
    'gopay' => 'GoPay'
];

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    
    // Validation checks
    if (empty($full_name)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid";
    }
    
    if (empty($phone) || !preg_match('/^[0-9]{10,13}$/', $phone)) {
        $errors[] = "Nomor telepon tidak valid (10-13 digit)";
    }
    
    if (empty($address)) {
        $errors[] = "Alamat harus diisi";
    }
    
    if (empty($payment_method) || !array_key_exists($payment_method, $payment_methods)) {
        $errors[] = "Metode pembayaran harus dipilih";
    }
    
    // If no errors, process the order
    if (empty($errors)) {
        try {
            // Start a database transaction
            $pdo->beginTransaction();
            
            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (
                user_id, 
                total_amount, 
                full_name, 
                email, 
                phone, 
                address, 
                payment_method, 
                status, 
                created_at
            ) VALUES (
                :user_id, 
                :total_amount, 
                :full_name, 
                :email, 
                :phone, 
                :address, 
                :payment_method, 
                'pending', 
                NOW()
            )");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':total_amount', $total_amount);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->execute();
            
            $order_id = $pdo->lastInsertId();
            
            
            // Insert order items

            
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (
                    order_id, 
                    product_id, 
                    quantity, 
                    price
                ) VALUES (
                    :order_id, 
                    :product_id, 
                    :quantity, 
                    :price
                )");
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':product_id', $item['product_id']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();
            }
            
            // Clear the cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            // Commit the transaction
            $pdo->commit();
            
            // Redirect to payment gateway or order confirmation
            header("Location: ../fungsi/payment_confirmation.php?order_id=" . $order_id);
            exit;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $pdo->rollBack();
            $errors[] = "Terjadi kesalahan saat memproses pesanan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Konfirmasi Pesanan</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="../css/checkout.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container checkout-container" style="padding-top: 150px; padding-bottom: 50px;">
        <div class="row">
            <div class="col-md-8">
                
                <?php 
                // Display errors
                if (!empty($errors)) {
                    echo '<div class="alert alert-danger">';
                    foreach ($errors as $error) {
                        echo '<p class="mb-1"><i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($error) . '</p>';
                    }
                    echo '</div>';
                }
                ?>

                <form method="post" id="checkoutForm" novalidate>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user me-2"></i>Informasi Pelanggan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Alamat Pengiriman</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?php 
                                        echo htmlspecialchars($user['address'] ?? ''); 
                                    ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-credit-card me-2"></i>Metode Pembayaran
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($payment_methods as $method_key => $method_name): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-option">
                                        <input class="form-check-input" type="radio" 
                                               name="payment_method" 
                                               id="payment_<?php echo $method_key; ?>" 
                                               value="<?php echo $method_key; ?>" 
                                               required>
                                        <label class="form-check-label" for="payment_<?php echo $method_key; ?>">
                                            <?php echo $method_name; ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-shopping-cart me-2"></i>Detail Pesanan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart_items as $item) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <h5 class="mb-0">Total Pembayaran</h5>
                                <h3 class="text-primary mb-0">
                                    Rp <?php echo number_format($total_amount, 0, ',', '.'); ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-lock me-2"></i>Bayar Sekarang
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="card sticky-top">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Informasi Penting
                    </div>
                    <div class="card-body">
                        <h6>Catatan Pembayaran:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Pastikan data diri sudah benar</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Pilih metode pembayaran dengan tepat</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Periksa kembali total pembayaran</li>
                        </ul>
                        <hr>
                        <h6>Keamanan Transaksi:</h6>
                        <p class="small text-muted">
                            <i class="fas fa-shield-alt text-primary me-2"></i>
                            Transaksi Anda dilindungi dengan enkripsi data yang aman
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Optional: Client-side form validation -->
    <script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
    </script>
</body>
</html>