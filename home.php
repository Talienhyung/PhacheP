<?php
session_start();
try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

// Supprimer un article
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM Article WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $stmt = $db->prepare("DELETE FROM Stock WHERE article_id = ?");
    $stmt->execute([$_GET['delete']]);
    $stmt = $db->prepare("DELETE FROM Cart WHERE article_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Liste des articles
$stmt = $db->query("SELECT A.*, S.quantity FROM Article A JOIN Stock S ON A.id = S.article_id ORDER BY A.id DESC");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CRUD Articles</title>
</head>
<body>

    <h2>Liste des Articles</h2>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th><th>Nom</th><th>Prix</th><th>Date</th><th>Auteur</th><th>Stock</th><th>Actions</th>
            </tr>
            <?php foreach ($articles as $article): ?>
            <tr>
                <td><?= $article['id'] ?></td>
                <td><?= htmlspecialchars($article['name']) ?></td>
                <td><?= number_format($article['price'], 2) ?> €</td>
                <td><?= $article['publication_date'] ?></td>
                <td><?= $article['author_id'] ?></td>
                <td><?= $article['quantity'] ?></td>
                <td>
                    <?php
                    if ($article['author_id'] == $_SESSION['id']) {
                        echo '<a href="article.php?edit=' . $article['id'] . '">✏️ Modifier</a> | <a href="?delete=' . $article['id'] . '" onclick="return confirm(\'Supprimer cet article ?\')">🗑️ Supprimer</a>';
                    } else {
                        echo '<a href="addcart.php?id=' . $article['id'] . '">🛒 Ajouter au panier</a>';
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </body>
</html>