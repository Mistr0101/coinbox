<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';
$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plan = $_POST['plan'];

    // Determine the duration and price based on the selected plan
    switch ($plan) {
        case '1_month':
            $duration = '1 MONTH';
            $price = '9.99';
            break;
        case '6_months':
            $duration = '6 MONTH';
            $price = '49.99';
            break;
        case '1_year':
            $duration = '1 YEAR';
            $price = '89.99';
            break;
        default:
            die('Invalid plan selected.');
    }

    try {
        // Add or update the subscription in the database
        $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, start_date, end_date, amount, status) 
                               VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL $duration), ?, 'active')
                               ON DUPLICATE KEY UPDATE start_date = NOW(), end_date = DATE_ADD(NOW(), INTERVAL $duration), amount = ?, status = 'active'");
        $stmt->execute([$userId, $price, $price]);

        // Redirect to the dashboard or a confirmation page
        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        die('Database Error: ' . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>
