<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Omivahi</title>
    <style>
        body {
            font-family: Georgia, serif;
            background: #f5f5f5;
            padding: 40px;
        }
        form {
            background: white;
            padding: 20px;
            border: 2px solid #ccc;
            width: 400px;
            margin: auto;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }
        input, select {
            width: 100%;
            margin-bottom: 12px;
            padding: 8px;
            border: 1px solid #999;
        }
        button {
            background-color: #333;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        .message {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Inscription au royaume d'Omivahi</h2>
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="email" name="email" placeholder="Adresse e-mail" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="text" name="profile_picture" placeholder="Lien de la photo (facultatif)">
        <button type="submit" name="register">S'enregistrer</button>
    </form>

    <?php
    session_start();
    if (isset($_POST['register'])) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Sécurisation des champs
            $username = htmlspecialchars($_POST['username']);
            $email = htmlspecialchars($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $profile_picture = !empty($_POST['profile_picture']) ? htmlspecialchars($_POST['profile_picture']) : null;
            $role = "";

            // Vérifie si l'email existe déjà
            $check = $db->prepare("SELECT COUNT(*) FROM `User` WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetchColumn() > 0) {
                echo "<p class='message' style='color:red;'>Cette adresse e-mail est déjà utilisée.</p>";
            } else {
                // Insertion dans la table
                $insert = $db->prepare("INSERT INTO `User` (username, password, email, profile_picture, role) VALUES (?, ?, ?, ?, ?)");
                $insert->execute([$username, $password, $email, $profile_picture, $role]);

                $userId = $db->lastInsertId();
                $_SESSION["id"] = $userId;

                echo "<p class='message' style='color:green;'>Inscription réussie, noble voyageur ! ID utilisateur : $userId</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='message' style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
        }
    }
    ?>

    <p style="text-align:center; margin-top: 30px;">
    <a href="/phachep/login.php" style="display:inline-block; background:#333; color:white; padding:10px 20px; border-radius:5px; text-decoration:none;">
        retour
    </a>
    </p>

</body>
</html>
