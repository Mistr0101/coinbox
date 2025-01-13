<?php

// Assurez-vous que le fichier config.php est inclus
require_once 'config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Vérifiez si la variable $pdo est définie
        if (!isset($pdo)) {
            throw new Exception("Database connection not established.");
        }

        // Insérer l'utilisateur
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        $userId = $pdo->lastInsertId();

        // Rediriger vers la page de paiement
        header("Location: payment.php?user_id=" . $userId);
        exit;
    } catch (PDOException $e) {
        echo 'Database Error: ' . $e->getMessage();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

<form method="post" action="register.php">
    Username: <input type="text" name="username" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
