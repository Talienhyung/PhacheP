<?php
include 'header.php';
require_once 'db_config.php';
try {
    $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

error_reporting(E_ERROR | E_PARSE); // Affiche uniquement les erreurs critiques
ini_set('display_errors', 0);  

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
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$order = isset($_GET['order']) ? $_GET['order'] : 'id_desc';

$orderBy = 'A.id DESC';
switch ($order) {
    case 'name_asc':
        $orderBy = 'A.name ASC';
        break;
    case 'name_desc':
        $orderBy = 'A.name DESC';
        break;
    case 'price_asc':
        $orderBy = 'A.price ASC';
        break;
    case 'price_desc':
        $orderBy = 'A.price DESC';
        break;
    case 'date_asc':
        $orderBy = 'A.publication_date ASC';
        break;
    case 'date_desc':
        $orderBy = 'A.publication_date DESC';
        break;
    case 'user_asc':
        $orderBy = 'User.username ASC';
        break;
    case 'user_desc':
        $orderBy = 'User.username DESC';
        break;
    case 'stock_asc':
        $orderBy = 'S.quantity ASC';
        break;
    case 'stock_desc':
        $orderBy = 'S.quantity DESC';
        break;
}

if ($search !== '') {
    $stmt = $db->prepare("SELECT A.*, S.quantity, User.username FROM Article A JOIN Stock S ON A.id = S.article_id JOIN User ON A.author_id = User.id WHERE A.name LIKE ? ORDER BY $orderBy");
    $stmt->execute(['%' . $search . '%']);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $db->query("SELECT A.*, S.quantity, User.username FROM Article A JOIN Stock S ON A.id = S.article_id JOIN User ON A.author_id = User.id ORDER BY $orderBy");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
    <form method="get" style="text-align:center; margin-bottom:1em;">
        <input type="text" id="search" name="search" placeholder="Rechercher un article..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Rechercher</button>
    </form>

    <table>
        <tr>
            <th>Image</th>
            <th>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="order" value="<?= $order === 'name_asc' ? 'name_desc' : 'name_asc' ?>">
                    <?php if ($search !== ''): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>
                    <button type="submit" style="background:none;border:none;cursor:pointer;">Nom <?= $order === 'name_asc' ? '▲' : ($order === 'name_desc' ? '▼' : '') ?></button>
                </form>
            </th>
            <th>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="order" value="<?= $order === 'price_asc' ? 'price_desc' : 'price_asc' ?>">
                    <?php if ($search !== ''): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>
                    <button type="submit" style="background:none;border:none;cursor:pointer;">Prix <?= $order === 'price_asc' ? '▲' : ($order === 'price_desc' ? '▼' : '') ?></button>
                </form>
            </th>
            <th>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="order" value="<?= $order === 'date_asc' ? 'date_desc' : 'date_asc' ?>">
                    <?php if ($search !== ''): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>
                    <button type="submit" style="background:none;border:none;cursor:pointer;">Date <?= $order === 'date_asc' ? '▲' : ($order === 'date_desc' ? '▼' : '') ?></button>
                </form>
            </th>
            <th>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="order" value="<?= $order === 'user_asc' ? 'user_desc' : 'user_asc' ?>">
                    <?php if ($search !== ''): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>
                    <button type="submit" style="background:none;border:none;cursor:pointer;">Auteur <?= $order === 'user_asc' ? '▲' : ($order === 'user_desc' ? '▼' : '') ?></button>
                </form>
            </th>
            <th>
                <form method="get" style="display:inline;">
                    <input type="hidden" name="order" value="<?= $order === 'stock_asc' ? 'stock_desc' : 'stock_asc' ?>">
                    <?php if ($search !== ''): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>
                    <button type="submit" style="background:none;border:none;cursor:pointer;">Stock <?= $order === 'stock_asc' ? '▲' : ($order === 'stock_desc' ? '▼' : '') ?></button>
                </form>
            </th>
            <th>Actions</th></form>
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
