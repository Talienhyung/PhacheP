<?php
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
    <h2><a href="cart.php">Mon Panier</a></h2>

    <hr>
    <h2>Mes factures</h2>
    <ul>
    <?php foreach ($invoices as $inv): ?>
        <li><?= htmlspecialchars($inv['transaction_date']) ?> – <?= number_format($inv['amount'], 2) ?> €</li>
    <?php endforeach; ?>
    </ul>

    <hr>
    <h2>Modifier mes informations</h2>
    <?php
    // Traitement du formulaire de modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isYourself) {
        $newUsername = trim($_POST['username']);
        $newEmail = trim($_POST['email']);

        if ($newUsername && $newEmail) {
            $stmt = $db->prepare("UPDATE User SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$newUsername, $newEmail, $userId]);
            // Met à jour les infos affichées
            $user['username'] = $newUsername;
            $user['email'] = $newEmail;
            echo "<p style='color:green;'>Profil mis à jour !</p>";
        } else {
            echo "<p style='color:red;'>Veuillez remplir tous les champs.</p>";
        }
    }
    ?>
    <form method="POST">
        <label>Nom d'utilisateur : <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></label><br>
        <label>Nouvel email : <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></label><br>
        <button type="submit">Mettre à jour</button>
    </form>

    <hr>
    <h2>Ajouter de l'argent</h2>
    <?php
    // Traitement de l'ajout d'argent
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['montant']) && $isYourself) {
        $montant = floatval($_POST['montant']);
        if ($montant > 0) {
            $stmt = $db->prepare("UPDATE User SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$montant, $userId]);
            // Met à jour le solde affiché
            $user['balance'] += $montant;
            echo "<p style='color:green;'>$montant € ajoutés à votre solde !</p>";
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $userId);
        } else {
            echo "<p style='color:red;'>Veuillez entrer un montant valide.</p>";
        }
    }
    ?>
    <form method="post">
        <label>Montant (€) : <input type="number" step="0.01" name="montant" required></label><br>
        <button type="submit">Ajouter</button>
    </form>

<?php endif; ?>
</body>
</html>
