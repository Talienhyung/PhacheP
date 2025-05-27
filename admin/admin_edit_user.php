<?php
include "../header.php";
require_once "../auth.php";

try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
require_once "admin_auth.php";
// Check for a valid user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID utilisateur invalide.");
}

$userId = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $balance = $_POST['balance'] ?? 0;

    $update = $db->prepare("UPDATE User SET username = ?, email = ?, balance = ? WHERE id = ?");
    $update->execute([$username, $email, $balance, $userId]);

    echo "<p style='color:green;'>Utilisateur mis à jour avec succès.</p>";
}

// Fetch current user data
$stmt = $db->prepare("SELECT * FROM User WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur non trouvé.");
}
?>

<h1>Modifier l'utilisateur #<?= htmlspecialchars($user['id']) ?></h1>

<form method="post">
    <label>Nom d'utilisateur :<br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
    </label><br><br>

    <label>Email :<br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </label><br><br>

    <label>Solde (€) :<br>
        <input type="number" step="0.01" name="balance" value="<?= htmlspecialchars($user['balance']) ?>" required>
    </label><br><br>

    <button type="submit">Enregistrer les modifications</button>
    <a href="admin_users.php">Annuler</a>
</form>
