<?php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Omivahi</title>
    <style>
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #ccc;
            width: 500px;
            margin: auto;
        }
        input, select {
            width: 80%;
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

</body>
</html>
