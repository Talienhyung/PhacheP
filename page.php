<?php
include 'header.php';
require_once "auth.php";

require_once 'db_config.php';
try {
    $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

$userId = $_GET["id"];
$query = $db->prepare("SELECT username, email, balance, profile_picture, role FROM User WHERE id = ?");
$query->execute([$userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<p style='color:red;'>Utilisateur introuvable... Peut-être a-t-il été banni du royaume ?</p>";
    exit();
}

$isYourself = $userId == $_SESSION["id"];

// Récupère les articles publiés
$stmt = $db->prepare("SELECT id, name, price FROM Article WHERE author_id = ?");
$stmt->execute([$userId]);
$articles = $stmt->fetchAll();

$purchased = [];
$invoices = [];

if ($isYourself) {
    $stmt = $db->prepare("
        SELECT A.id, A.name, A.price
        FROM Cart C
        JOIN Article A ON C.article_id = A.id
        WHERE C.user_id = ?
    ");
    $stmt->execute([$userId]);
    $purchased = $stmt->fetchAll();

    $stmt = $db->prepare("SELECT * FROM Invoice WHERE user_id = ?");
    $stmt->execute([$userId]);
    $invoices = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil de <?= htmlspecialchars($user['username']) ?></title>
    <style>
        section {
            border: 1px solid #ccc;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        h2 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 0.3rem;
        }
        img {
            max-width: 150px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <main>
        <section>
            <h1>Bienvenue, <?= htmlspecialchars($user['username']) ?> !</h1>
            <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>
            <p><strong>Solde :</strong> <?= number_format($user['balance'], 2) ?> pièces d’or</p>

            <?php if (!empty($user['profile_picture'])): ?>
                <p><strong>Portrait royal :</strong><br>
                <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Photo de profil"></p>
            <?php else: ?>
                <p><em>Aucune image de profil renseignée...</em></p>
            <?php endif; ?>

            <p><?= $isYourself ? 'Votre compte' : 'Pas votre compte' ?></p>
        </section>

        <section>
            <h2>Articles publiés</h2>
            <ul>
                <?php foreach ($articles as $a): ?>
                    <li><?= htmlspecialchars($a['name']) ?> – <?= number_format($a['price'], 2) ?> €</li>
                <?php endforeach; ?>
            </ul>
        </section>

        <?php if ($isYourself): ?>
            <section>
                <h2>Mes factures</h2>
                <ul>
                    <?php foreach ($invoices as $inv): ?>
                        <li><?= htmlspecialchars($inv['transaction_date']) ?> – <?= number_format($inv['amount'], 2) ?> € <a href="invoice.php?id=<?=$inv['id']?>">Detail</a></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section>
                <h2>Modifier mes informations</h2>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['email'])) {
                    $newUsername = trim($_POST['username']);
                    $newEmail = trim($_POST['email']);

                    if ($newUsername && $newEmail) {
                        $stmt = $db->prepare("UPDATE User SET username = ?, email = ? WHERE id = ?");
                        $stmt->execute([$newUsername, $newEmail, $userId]);
                        $user['username'] = $newUsername;
                        $user['email'] = $newEmail;
                        echo "<p style='color:green;'>Profil mis à jour !</p>";
                    } else {
                        echo "<p style='color:red;'>Veuillez remplir tous les champs.</p>";
                    }
                }
                ?>
                <form method="POST">
                    <label>Nom d'utilisateur : <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required></label><br>
                    <label>Nouvel email : <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br>
                    <button type="submit">Mettre à jour</button>
                </form>
            </section>

            <section>
                <h2>Ajouter de l'argent</h2>
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['montant'])) {
                    $montant = floatval($_POST['montant']);
                    if ($montant > 0) {
                        $stmt = $db->prepare("UPDATE User SET balance = balance + ? WHERE id = ?");
                        $stmt->execute([$montant, $userId]);
                        $user['balance'] += $montant;
                        echo "<p style='color:green;'>$montant € ajoutés à votre solde !</p>";
                        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $userId);
                        exit();
                    } else {
                        echo "<p style='color:red;'>Veuillez entrer un montant valide.</p>";
                    }
                }
                ?>
                <form method="post">
                    <label>Montant (€) : <input type="number" step="0.01" name="montant" required></label><br>
                    <button type="submit">Ajouter</button>
                </form>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
