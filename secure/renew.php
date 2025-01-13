<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'config.php';
$userId = $_SESSION['user_id'];

try {
    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // Récupérer les informations d'abonnement
    $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = ?");
    $stmt->execute([$userId]);
    $subscription = $stmt->fetch();
} catch (PDOException $e) {
    die('Database Error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <p>Subscription Status: <?php echo htmlspecialchars($subscription['status']); ?></p>
    <p>Subscription End Date: <?php echo htmlspecialchars($subscription['end_date']); ?></p>

    <form method="post" action="renew.php">
        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
        <button type="submit">Renew Subscription</button>
    </form>
</body>
</html>
