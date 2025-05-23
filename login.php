<?php

include_once "header.php";
session_start();
try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = $db->prepare("SELECT * FROM User WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["id"] = $user["id"];

        // Redirection royale
        header("Location: page.php?id=" . $user["id"]);
        exit();
    } else {
        $message = "Accès refusé, noble âme : identifiants invalides.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Royale</title>
</head>
<body>
    <h1>Connexion au royaume</h1>

    <?php if ($message): ?>
        <p style="color:red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Adresse e-mail :</label><br>
        <input type="email" name="email" required><br><br>

        <label for="password">Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Connexion">
    </form>

    <p>Pas encore parmi nous ? <a href="/phachep/register.php">Créer un compte</a></p>
</body>
</html>
