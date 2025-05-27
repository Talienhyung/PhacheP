<?php
$db = new PDO('mysql:host=localhost;dbname=phachepDB;charset=utf8', 'root', '');

include 'header.php';
require_once 'auth.php';

$query = $db->prepare("SELECT * FROM User WHERE id = ?");
$query->execute([$_SESSION['id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

$isEdit = isset($_GET['edit']);
if ($isEdit) {
    $stmt = $db->prepare("SELECT * FROM Article WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $article = $stmt->fetch();
    if (!$article) {
        echo "<p style='color:red;'>Article introuvable...</p>";
        exit();
    }
    if ($article['author_id'] != $_SESSION['id'] && $user['role'] !== 'admin') {
        echo "<p style='color:red;'>Vous n'avez pas le droit de modifier cet article !</p>";
        exit();
    }
}

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
    header("Location:index.php");
    exit;
}

$editArticle = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM Article WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editArticle = $stmt->fetch();

    $stmt = $db->prepare("SELECT * FROM Stock WHERE article_id = ?");
    $stmt->execute([$_GET['edit']]);
    $stock = $stmt->fetch();
    $editArticle['stock'] = $stock ? $stock['quantity'] : 1;
}

if (isset($_POST['update'])) {
    $stmt = $db->prepare("UPDATE Article SET name=?, description=?, price=?, image_link=? WHERE id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['price'],
        $_POST['image_link'],
        $_POST['id']
    ]);

    $stmt = $db->prepare("UPDATE Stock SET quantity=? WHERE article_id=?");
    $stmt->execute([
        $_POST['stock'],
        $_POST['id']
    ]);
    header("Location:index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $editArticle ? "Modifier l'article" : "Ajouter un article" ?></title>
    <style>
        main {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 1.5rem;
            border: 1px solid #ccc;
        }
        h1, h2 {
            margin: 0 0 1rem;
            font-size: 1.2rem;
            color: #222;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        label {
            font-size: 0.95rem;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 0.4rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            background: #fefefe;
        }
        textarea {
            resize: vertical;
        }
        button {
            padding: 0.5rem;
            background: #2c7;
            color: black;
            font-weight: bold;
            border: 1px solid #2c7;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background: #1a5;
        }
    </style>
</head>
<body>
<main>
    <h1>Gestion des Articles</h1>
    <h2><?= $editArticle ? "Modifier l'article" : "Ajouter un article" ?></h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $editArticle['id'] ?? '' ?>">

        <label>
            Nom :
            <input type="text" name="name" value="<?= $editArticle['name'] ?? '' ?>" required>
        </label>

        <label>
            Description :
            <textarea name="description" rows="3"><?= $editArticle['description'] ?? '' ?></textarea>
        </label>

        <label>
            Prix (€) :
            <input type="number" step="0.01" name="price" value="<?= $editArticle['price'] ?? '' ?>" required>
        </label>

        <label>
            Lien image :
            <input type="text" name="image_link" value="<?= $editArticle['image_link'] ?? '' ?>">
        </label>

        <label>
            Stock :
            <input type="number" name="stock" value="<?= $editArticle['stock'] ?? 1 ?>">
        </label>

        <button type="submit" name="<?= $editArticle ? 'update' : 'add' ?>">
            <?= $editArticle ? 'Mettre à jour' : 'Ajouter' ?>
        </button>
    </form>
</main>
</body>
</html>
