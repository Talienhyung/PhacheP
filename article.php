<?php
$db = new PDO('mysql:host=localhost;dbname=phachepDB;charset=utf8', 'root', '');

require_once 'auth.php';

// Ajouter un article
if (isset($_POST['add'])) {
    $stmt = $db->prepare("INSERT INTO Article (name, description, price, publication_date, author_id, image_link)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['price'],
        date('Y-m-d H:i:s'),
        $_SESSION['id'],
        $_POST['image_link']
    ]);
    $stmt = $db->prepare("INSERT INTO Stock (article_id, stock) VALUES (?, ?)");
    $stmt->execute([
        $db->lastInsertId(),
        $_POST['stock']
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Supprimer un article
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM Article WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $stmt = $pdo->prepare("DELETE FROM Stock WHERE article_id = ?");
    $stmt->execute([$_GET['delete']]);
    $stmt = $pdo->prepare("DELETE FROM Cart WHERE article_id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Modifier un article (affichage dans le formulaire)
$editArticle = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM Article WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editArticle = $stmt->fetch();
}

// Enregistrer la modification
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE Article SET name=?, description=?, price=?, image_link=? WHERE id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['price'],
        $_POST['image_link'],
        $_POST['id']
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);

    $stmt = $pdo->prepare("UPDATE Stock SET stock=? WHERE article_id=?");
    $stmt->execute([
        $_POST['stock'],
        $_POST['id']
    ]);
    exit;
}

// Liste des articles
// $articles = $pdo->query("SELECT * FROM Article ORDER BY id DESC")->fetchAll();
// Liste des articles avec stock
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
    <h1>Gestion des Articles</h1>

    <h2><?= $editArticle ? "Modifier l'article" : "Nouvel article" ?></h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $editArticle['id'] ?? '' ?>">
        <label>Nom : <input type="text" name="name" value="<?= $editArticle['name'] ?? '' ?>" required></label><br>
        <label>Description : <textarea name="description"><?= $editArticle['description'] ?? '' ?></textarea></label><br>
        <label>Prix : <input type="number" step="0.01" name="price" value="<?= $editArticle['price'] ?? '' ?>" required></label><br>
        <label>Lien image : <input type="text" name="image_link" value="<?= $editArticle['image_link'] ?? '' ?>"></label><br>
        <label>Stock : <input type="number" name="stock" value="<?= $editArticle['stock'] ?? 1 ?>" ></label><br>
        <button type="submit" name="<?= $editArticle ? 'update' : 'add' ?>">
            <?= $editArticle ? 'Mettre à jour' : 'Ajouter' ?>
        </button>
    </form>

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
                <a href="?edit=<?= $article['id'] ?>">✏️ Modifier</a> |
                <a href="?delete=<?= $article['id'] ?>" onclick="return confirm('Supprimer cet article ?')">🗑️ Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
