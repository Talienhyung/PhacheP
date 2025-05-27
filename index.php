<?php
include 'header.php';

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
$stmt = $db->query("SELECT A.*, S.quantity, User.username FROM Article A JOIN Stock S ON A.id = S.article_id Join User ON A.author_id = User.id ORDER BY A.id DESC");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CRUD Articles</title>
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 1em auto;
            border: 1px solid #ccc;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: center;
        }
        img.thumb {
            max-width: 60px;
            max-height: 60px;
            object-fit: contain;
        }
    </style>
</head>
<body>

    <h2 style="text-align:center;">Liste des Articles</h2>
    <table>
        <tr>
            <th>Image</th><th>Nom</th><th>Prix</th><th>Date</th><th>Auteur</th><th>Stock</th><th>Actions</th>
        </tr>
        <?php foreach ($articles as $article): ?>
        <tr>
            <td>
                <?php if (!empty($article['image_link'])): ?>
                    <img src="<?= htmlspecialchars($article['image_link']) ?>" alt="<?= htmlspecialchars($article['name']) ?>" class="thumb">
                <?php else: ?>
                    <em>Pas d’image</em>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($article['name']) ?></td>
            <td><?= number_format($article['price'], 2) ?> €</td>
            <td><?= $article['publication_date'] ?></td>
            <td><?= $article['username'] ?></td>
            <td><?= $article['quantity'] ?></td>
            <td>
                <?php
                if ($article['author_id'] == $_SESSION['id']) {
                    echo '<a href="article.php?edit=' . $article['id'] . '">✏️ Modifier</a> | <a href="?delete=' . $article['id'] . '" onclick="return confirm(\'Supprimer cet article ?\')">🗑️ Supprimer</a>';
                } else {
                    echo '<a href="addcart.php?id=' . $article['id'] . '">Detail / Ajouter au panier</a>';
                }
                
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
