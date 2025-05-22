<?php
require_once "auth.php";

try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

$userId = $_SESSION["id"];
$query = $db->prepare("SELECT username, email, balance, profile_picture, role FROM User WHERE id = ?");
$query->execute([$userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color:red;'>Utilisateur introuvable... Peut-être a-t-il été banni du royaume ?</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil de <?php echo htmlspecialchars($user['username']); ?></title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?> !</h1>

    <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Rôle :</strong> <?php echo htmlspecialchars($user['role']); ?></p>
    <p><strong>Solde :</strong> <?php echo number_format($user['balance'], 2); ?> pièces d’or</p>

    <?php if (!empty($user['profile_picture'])): ?>
        <p><strong>Portrait royal :</strong><br>
        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Photo de profil" style="max-width: 150px;"></p>
    <?php else: ?>
        <p><em>Aucune image de profil renseignée...</em></p>
    <?php endif; ?>
</body>
</html>
