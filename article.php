<?php
$db = new PDO('mysql:host=localhost;dbname=phachepDB;charset=utf8', 'root', '');

require_once 'auth.php';

$isEdit = isset($_GET['edit']);
if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM Article WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $article = $stmt->fetch();
    if (!$article) {
        echo "<p style='color:red;'>Article introuvable...</p>";
        exit();
    }
    if ($article['author_id'] != $_SESSION['id']) {
        echo "<p style='color:red;'>Vous n'avez pas le droit de modifier cet article ! C'est pas bien :(</p>";
        exit();
    }
}

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
    $stmt = $db->prepare("INSERT INTO Stock (article_id, quantity) VALUES (?, ?)");
    $stmt->execute([
        $db->lastInsertId(),
        $_POST['stock']
    ]);
    header("Location:home.php");
    exit;
}

// Modifier un article (affichage dans le formulaire)
$editArticle = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM Article WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editArticle = $stmt->fetch();

    $stmt = $db->prepare("SELECT * FROM Stock WHERE article_id = ?");
    $stmt->execute([$_GET['edit']]);
    $stock = $stmt->fetch();
    if ($stock) {
        $editArticle['stock'] = $stock['quantity'];
    } else {
        $editArticle['stock'] = 1; // Valeur par défaut si pas de stock
    }
}

// Enregistrer la modification
if (isset($_POST['update'])) {
    $stmt = $db->prepare("UPDATE Article SET name=?, description=?, price=?, image_link=? WHERE id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['price'],
        $_POST['image_link'],
        $_POST['id']
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);

    $stmt = $db->prepare("UPDATE Stock SET quantity=? WHERE article_id=?");
    $stmt->execute([
        $_POST['stock'],
        $_POST['id']
    ]);
    header("Location:home.php");
    exit;
}


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
</body>
</html>
