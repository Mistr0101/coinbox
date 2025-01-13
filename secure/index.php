<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Identifiant et mot de passe incorrects !";
            }
        } catch (PDOException $e) {
            $error = 'Database Error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['register'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        try {
            if (!isset($pdo)) {
                throw new Exception("Database connection not established.");
            }

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password]);
            $userId = $pdo->lastInsertId();

            header("Location: payment.php?user_id=" . $userId);
            exit;
        } catch (PDOException $e) {
            $error = 'Database Error: ' . $e->getMessage();
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8" />
    <title>Login</title>
    <link rel="icon" href="https://product-select.com/wp-content/uploads/2024/03/cropped-darkether-1-32x32.png" sizes="32x32" />
    <link rel="icon" href="https://product-select.com/wp-content/uploads/2024/03/cropped-darkether-1-192x192.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://product-select.com/wp-content/uploads/2024/03/cropped-darkether-1-180x180.png" />
    <meta name="msapplication-TileImage" content="https://product-select.com/wp-content/uploads/2024/03/cropped-darkether-1-270x270.png" />
    <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, minimum-scale=1">
    <meta name="description" content=""/>
    <meta http-equiv="X-UA-Compatible" content="" />
    <meta property="og:site_name" content="product-select - "/>
    <meta property="og:title" content="Login"/>
    <meta property="og:type" content="Maintenance"/>
    <meta property="og:url" content="https://product-select.com"/>
    <meta property="og:description" content="Login to access your account"/>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=Open%20Sans:300,300italic,regular,italic,600,600italic,700,700italic,800,800italic:Latin">
    <style>
        body {
            background-color: #111111;
            font-family: 'Open Sans', sans-serif;
            color: #ffffff;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('https://product-select.com/wp-content/uploads/2024/06/mt-sample-background.jpg') no-repeat center center fixed;
            background-size: cover;
            transition: transform 0.5s ease;
        }
        .main-container.shifted {
            transform: translateX(-2%); /* Ajustement pour faire suivre le cadenas */
        }
        .login-form-container {
            position: absolute;
            top: 0;
            right: 0;
            width: 20%; /* Ajustement de la largeur */
            height: 100%;
            background-color: #111111;
            padding: 20px;
            box-shadow: -10px 0 20px rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }
        .login-form-container.active {
            display: flex;
        }
        .login-form, .register-form {
            width: 100%;
            display: none;
        }
        .login-form.active, .register-form.active {
            display: block;
        }
        .login-form label, .register-form label {
            font-size: 1.2em;
            margin-bottom: 10px;
            display: block;
        }
        .login-form input, .register-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ffffff;
            border-radius: 5px;
            background-color: #111111;
            color: #ffffff;
        }
        .login-form input::placeholder, .register-form input::placeholder {
            color: #ffffff;
        }
        .login-form .button, .register-form .button {
            width: 100%;
            padding: 10px;
            background-color: #2ecc71;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
        }
        .login-form .button:hover, .register-form .button:hover {
            background-color: #27ae60;
        }
        .login-form a, .register-form a {
            color: #ffffff;
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-open-login-form {
            position: absolute;
            top: 50%;
            right: 0%; /* Déplacement initial à droite */
            width: 60px;
            height: 60px;
            background-color: #111111;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2em;
            color: #ffffff;
            transition: background-color 0.3s, color 0.3s, right 0.5s;
            transform: translateY(-50%);
        }
        .btn-open-login-form.error img {
            content: url('img/cadenas_erreur.png'); /* Chemin vers l'image du cadenas en erreur */
        }
        .login-error {
            color: red;
            text-align: center;
        }
        .lock-icon {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="maintenance">

<div class="main-container">
    <div class="login-form-container">
        <?php if (!empty($error)): ?>
            <p class="login-error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form id="login-form" class="login-form active" method="post">
            <label>User Login</label>
            <input type="text" name="username" id="log" class="input username" placeholder="Identifiant" required />
            <input type="password" name="password" id="login_password" class="input password" placeholder="Mot de passe" required />
            <input type="submit" class="button" name="login" id="submit" value="Login" />
            <a id="show-register-form">Register</a>
        </form>
        <form id="register-form" class="register-form" method="post">
            <label>Register</label>
            <input type="text" name="username" class="input username" placeholder="Identifiant" required />
            <input type="email" name="email" class="input email" placeholder="Email" required />
            <input type="password" name="password" class="input password" placeholder="Mot de passe" required />
            <input type="submit" class="button" name="register" value="Register" />
            <a id="show-login-form">Login</a>
        </form>
    </div>
    <div id="btn-open-login-form" class="btn-open-login-form <?php if (!empty($error)) echo 'error'; ?>">
        <img src="img/cadenas_ferme.png" class="lock-icon" alt="lock icon">
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#btn-open-login-form').click(function() {
            var mainContainer = $('.main-container');
            var lockButton = $(this);
            mainContainer.toggleClass('shifted');
            $('.login-form-container').toggleClass('active');
            
            if (mainContainer.hasClass('shifted')) {
                lockButton.css('right', 'calc(20% + 30px)'); // Ajustement pour faire suivre le cadenas
                lockButton.find('img').attr('src', 'img/cadenas_ouvert.png');
            } else {
                lockButton.css('right', '0');
                lockButton.find('img').attr('src', 'img/cadenas_ferme.png');
            }
        });
        $('#show-register-form').click(function() {
            $('#login-form').removeClass('active');
            $('#register-form').addClass('active');
        });
        $('#show-login-form').click(function() {
            $('#register-form').removeClass('active');
            $('#login-form').addClass('active');
        });
    });

    <?php if (!empty($error)): ?>
        $(document).ready(function() {
            $('#btn-open-login-form').addClass('error');
        });
    <?php endif; ?>
</script>

</body>
</html>
