<?php
require_once 'config.php';

// Retrieve all products from the database
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="../css/produk.css" rel="stylesheet">
</head>
<body>

<!-- navbar.php -->
<?php include 'navbar.php'; ?>
<div class="container my-5" style="padding-top: 56px;">

    <?php 
    // Display success message if set
    if (isset($_SESSION['success_message'])) { 
    ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Clear the message after displaying
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php 
    } 
    // Display error message if set
    if (isset($_SESSION['error_message'])) { 
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']); // Clear the message after displaying
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php 
    } 
    ?>

    <!-- Display Products -->
    <div class="row">
        <?php foreach ($products as $product) { ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($product['image']) { ?>
                        <img src="images/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                    <?php } else { ?>
                        <img src="/api/placeholder/400/320" class="card-img-top" alt="<?php echo $product['name']; ?>">
                    <?php } ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="card-text text-muted mb-2"><?php echo $product['description']; ?></p>
                        <p class="card-text"><small class="text-muted"><i class="fas fa-tag me-2"></i><?php echo $product['brand']; ?></small></p>
                        <div class="price-badge mb-2">
                            <i class="fas fa-money-bill-wave me-2"></i>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                        </div>
                        <p class="card-text"><small class="text-muted"><i class="fas fa-box me-2"></i>Stok: <?php echo $product['stock']; ?></small></p>
                        <div class="mt-auto d-flex gap-2">
                            <a href="../fungsi/add_cart.php?id=<?php echo $product['id']; ?>" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-shopping-cart me-2"></i>Tambah ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
