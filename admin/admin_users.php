<?php

include "../header.php";
require_once "../auth.php";

require_once '../db_config.php';
try {
    $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

require_once "admin_auth.php";

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    // Supprimer les articles du panier liés aux articles de l'utilisateur
    $stmt = $db->prepare("
        DELETE FROM Cart 
        WHERE article_id IN (SELECT id FROM Article WHERE author_id = ?)
    ");
    $stmt->execute([$userId]);

    // Supprimer les stocks des articles de l'utilisateur
    $stmt = $db->prepare("
        DELETE FROM Stock 
        WHERE article_id IN (SELECT id FROM Article WHERE author_id = ?)
    ");
    $stmt->execute([$userId]);

    // Supprimer les articles de l'utilisateur
    $stmt = $db->prepare("DELETE FROM Article WHERE author_id = ?");
    $stmt->execute([$userId]);

    // Supprimer l'utilisateur
    $stmt = $db->prepare("DELETE FROM User WHERE id = ?");
    $stmt->execute([$userId]);
}

// Récupération des utilisateurs
$stmt = $db->query("SELECT id, username, email, balance, role FROM User WHERE role != 'admin' ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Gestion des utilisateurs</h1>
<a href="admin_articles.php">Gérer les articles</a>

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Solde</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= number_format($user['balance'], 2) ?> €</td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td>
                <?php
                echo '<a href="admin_edit_user.php?id=' . $user['id'] . '">✏️ Modifier</a> | <a href="?delete=' . $user['id'] . '" onclick="return confirm(\'Supprimer cet utilisateur ?\')">🗑️ Supprimer</a>';
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>