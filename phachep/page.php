<?php
include "header.php";
require_once "auth.php";

try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

$userId = $_GET["id"];
$query = $db->prepare("SELECT username, email, balance, profile_picture, role FROM User WHERE id = ?");
$query->execute([$userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);

$isYourself = $userId == $_SESSION["id"];

// Récupère les articles publiés
$stmt = $db->prepare("SELECT id, name, price FROM Article WHERE author_id = ?");
$stmt->execute([$userId]);
$articles = $stmt->fetchAll();

// Si c'est notre compte, on récupère les articles achetés et factures
$purchased = [];
$invoices = [];

if ($isYourself) {
    // Articles achetés (exemple basique)
    $stmt = $db->prepare("
        SELECT A.id, A.name, A.price
        FROM Cart C
        JOIN Article A ON C.article_id = A.id
        WHERE C.user_id = ?
    ");
    $stmt->execute([$userId]);
    $purchased = $stmt->fetchAll();

    // Factures
    $stmt = $db->prepare("SELECT * FROM Invoice WHERE user_id = ?");
    $stmt->execute([$userId]);
    $invoices = $stmt->fetchAll();
}

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
    <p><?= $isYourself ? 'Votre compte' : 'Pas votre compte' ?></p>

    <h2>Articles publiés</h2>
<ul>
<?php foreach ($articles as $a): ?>
    <li><?= htmlspecialchars($a['name']) ?> – <?= number_format($a['price'], 2) ?> €</li>
<?php endforeach; ?>
</ul>

<?php if ($isYourself): ?>

    <hr>
    <h2>Articles achetés</h2>
    <ul>
    <?php foreach ($purchased as $a): ?>
        <li><?= htmlspecialchars($a['name']) ?> – <?= number_format($a['price'], 2) ?> €</li>
    <?php endforeach; ?>
    </ul>

    <hr>
    <h2>Mes factures</h2>
    <ul>
    <?php foreach ($invoices as $inv): ?>
        <li><?= htmlspecialchars($inv['transaction_date']) ?> – <?= number_format($inv['amount'], 2) ?> €</li>
    <?php endforeach; ?>
    </ul>

    <hr>
    <h2>Modifier mes informations</h2>
    <form method="post" action="modifier_infos.php">
        <label>Nouvel email : <input type="email" name="email" required></label><br>
        <label>Nouveau mot de passe : <input type="password" name="password" required></label><br>
        <button type="submit">Mettre à jour</button>
    </form>

    <hr>
    <h2>Ajouter de l'argent</h2>
    <form method="post" action="ajouter_solde.php">
        <label>Montant (€) : <input type="number" step="0.01" name="montant" required></label><br>
        <button type="submit">Ajouter</button>
    </form>

<?php endif; ?>
</body>
</html>
