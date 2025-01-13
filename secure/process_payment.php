<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $plan = $_POST['plan'];
    $amount = $_POST['amount'];
    $sessionUserId = $_SESSION['user_id'];

    // Vérifiez que l'utilisateur connecté correspond à l'utilisateur de la requête
    if ($userId != $sessionUserId) {
        die('Unauthorized access.');
    }

    // Déterminer la durée de l'abonnement
    switch ($plan) {
        case '1_month':
            $duration = '1 MONTH';
            break;
        case '6_months':
            $duration = '6 MONTHS';
            break;
        case '1_year':
            $duration = '1 YEAR';
            break;
        default:
            die('Invalid subscription plan.');
    }

    // Simuler un paiement réussi pour cet exemple
    $paymentSuccess = true; // Remplacez ceci par une véritable logique de traitement de paiement

    if ($paymentSuccess) {
        try {
            // Vérifiez si l'utilisateur a déjà une entrée dans subscriptions
            $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ?");
            $stmt->execute([$userId]);
            $subscription = $stmt->fetch();

            if ($subscription && $subscription['end_date'] > date('Y-m-d')) {
                die('You already have an active subscription.');
            } else {
                // Insérer ou mettre à jour l'abonnement
                if ($subscription) {
                    $stmt = $pdo->prepare("UPDATE subscriptions SET end_date = DATE_ADD(end_date, INTERVAL $duration), amount = ?, status = 'active' WHERE user_id = ?");
                    $stmt->execute([$amount, $userId]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, start_date, end_date, amount, status) VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL $duration), ?, 'active')");
                    $stmt->execute([$userId, $amount]);
                }

                echo "Subscription renewed successfully!";
            }
        } catch (PDOException $e) {
            echo 'Database Error: ' . $e->getMessage();
        }
    } else {
        echo "Payment failed. Please try again.";
    }
} else {
    echo "Form not submitted...<br>"; // Message de débogage
}
?>
