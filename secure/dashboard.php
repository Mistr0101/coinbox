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

$hasActiveSubscription = $subscription && $subscription['end_date'] > date('Y-m-d');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            border-radius: 8px;
        }
        h1 {
            font-size: 2em;
            color: #333;
        }
        p {
            font-size: 1.2em;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 1em;
            color: #fff;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .button:hover {
            background-color: #2980b9;
        }
        .header, .footer {
            text-align: center;
            padding: 10px 0;
            background: #3498db;
            color: #ffffff;
        }
        .subscription-options {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .subscription-option {
            background: #eee;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 30%;
        }
        .subscription-option h3 {
            color: #333;
        }
        .subscription-option p {
            color: #666;
        }
        .subscription-option .button {
            background-color: #2ecc71;
        }
        .subscription-option .button:hover {
            background-color: #27ae60;
        }
        .logout {
            text-align: right;
            margin-top: -40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>My Dashboard</h2>
    </div>
    <div class="container">
        <div class="logout">
            <a href="logout.php" class="button">Logout</a>
        </div>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Subscription Status: <?php echo htmlspecialchars($subscription['status']); ?></p>
        <p>Subscription End Date: <?php echo htmlspecialchars($subscription['end_date']); ?></p>

        <?php if ($hasActiveSubscription): ?>
            <p>You already have an active subscription. You cannot purchase another subscription until the current one expires.</p>
        <?php else: ?>
            <p>Your subscription has expired, and your access is restricted. Please renew your subscription to regain full access:</p>
            <div class="subscription-options">
                <div class="subscription-option">
                    <h3>1 Month</h3>
                    <p>$9.99</p>
                    <form method="post" action="payment.php">
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                        <input type="hidden" name="plan" value="1_month">
                        <button type="submit" class="button">Choose</button>
                    </form>
                </div>
                <div class="subscription-option">
                    <h3>6 Months</h3>
                    <p>$49.99</p>
                    <form method="post" action="payment.php">
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                        <input type="hidden" name="plan" value="6_months">
                        <button type="submit" class="button">Choose</button>
                    </form>
                </div>
                <div class="subscription-option">
                    <h3>1 Year</h3>
                    <p>$89.99</p>
                    <form method="post" action="payment.php">
                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                        <input type="hidden" name="plan" value="1_year">
                        <button type="submit" class="button">Choose</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2024 Your Website. All rights reserved.</p>
    </div>
</body>
</html>

