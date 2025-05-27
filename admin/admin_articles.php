<?php

include "../header.php";
require_once "../auth.php";

try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

require_once "admin_auth.php";

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    // Delete from Stock first
    $stmt = $db->prepare("DELETE FROM Stock WHERE article_id = ?");
    $stmt->execute([$_GET['delete']]);
    // Then delete from Cart
    $stmt = $db->prepare("DELETE FROM Cart WHERE article_id = ?");
    $stmt->execute([$_GET['delete']]);
    // Finally delete from Article
    $stmt = $db->prepare("DELETE FROM Article WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Récupération des utilisateurs
$query = $db->prepare("SELECT Article.id, Article.name, Article.price, Article.description, Article.image_link, Article.author_id, S.quantity, U.username FROM Article JOIN Stock AS S ON Article.id = S.article_id JOIN User AS U ON Article.author_id = U.id");
$query->execute();	
$articles = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Gestion des Articles</h1>
<a href="admin_users.php">Gérer les utilisateurs</a>

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>desc</th>
            <th>Price</th>
            <th>img</th>
            <th>author</th>
            <th>quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= htmlspecialchars($article['id']) ?></td>
            <td><?= htmlspecialchars($article['name']) ?></td>
            <td><?= htmlspecialchars($article['description']) ?></td>
            <td><?= number_format($article['price'], 2) ?> €</td>
            <td>
                <?php if (!empty($article['image_link'])): ?>
                    <img src="<?= htmlspecialchars($article['image_link']) ?>" alt="Article Image" style="max-width:100px;max-height:100px;">
                <?php else: ?>
                    No image
                <?php endif; ?>
            </td>
            <td><a href="../page.php?id=<?= $article['author_id'] ?>">
                <?= htmlspecialchars($article['username']) ?>
            </a></td>
            <td><?= htmlspecialchars($article['quantity']) ?></td>
            <td>
                <?php
                echo '<a href="../article.php?edit=' . $article['id'] . '">✏️ Modifier</a> | <a href="?delete=' . $article['id'] . '" onclick="return confirm(\'Supprimer cet article ?\')">🗑️ Supprimer</a>';
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>