<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SatelitCell - Smartphone Store</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/index.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section" style="padding-top: 125px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <h1 class="hero-title">Temukan Smartphone Impian Anda</h1>
                    <p class="hero-text">Koleksi smartphone terlengkap dengan harga terbaik dan garansi resmi. Dapatkan pengalaman berbelanja yang menyenangkan bersama SatelitCell.</p>
                    <a href="produk.php" class="btn hero-btn">
                        <i class="fas fa-shopping-bag me-2"></i>Belanja Sekarang
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="../images/bg2.png" alt="SateliteCell" class="img-fluid hero-image">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="container my-5 py-5">
        <h2 class="text-center mb-5" data-aos="fade-up">Produk Unggulan</h2>
        <div class="row">
            <?php
            $stmt = $pdo->query("SELECT * FROM products LIMIT 3");
            while ($row = $stmt->fetch()) {
            ?>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="<?php echo $row['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="card-price mb-0">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                            <span class="badge bg-success">Stok: <?php echo $row['stock']; ?></span>
                        </div>
                        <a href="cart.php?add=<?php echo $row['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Mengapa Memilih Kami?</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4>Produk Original</h4>
                        <p>Garansi resmi dan produk 100% original dari brand ternama</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h4>Pengiriman Cepat</h4>
                        <p>Pengiriman cepat ke seluruh Indonesia dengan packaging aman</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4>Layanan 24/7</h4>
                        <p>Customer service siap membantu Anda selama 24 jam</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Custom JS -->
    <script>
        AOS.init();
    </script>
</body>
</html>
