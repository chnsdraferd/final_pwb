<?php
function admin_only_allowed() {
    session_start();
    include 'config/database.php';

    $current_user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $current_user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user['role'] != "ADMIN"){
        header('Location: /tugas_besar/php/login.php');
        die();
    }
    
}