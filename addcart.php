<?php
require_once "auth.php";

try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

$userId = $_SESSION["id"];
$productId = $_GET["id"];
$query = $db->prepare("SELECT name, price, description, image_link, S.quantity FROM Article JOIN Stock as S ON Article.id = S.article_id WHERE Article.id = ?");
$query->execute([$productId]);
$product = $query->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "<p style='color:red;'>Produit introuvable... </p>";
    exit();
}

$queryCart = $db->prepare("SELECT COUNT(*) as count FROM Cart WHERE user_id = ? AND article_id = ?");
$queryCart->execute([$userId, $productId]);
$cartItem = $queryCart->fetch(PDO::FETCH_ASSOC);
$alreadyInCart = $cartItem ? (int)$cartItem['count'] : 0;

if (isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    $stmt = $db->prepare("INSERT INTO Cart (user_id, article_id) VALUES (?, ?)");
    for ($i = 0; $i < $quantity; $i++) {
        $stmt->execute([$userId, $productId]);
    }
    header("Location: cart.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .product {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100px;
            margin-right: 20px;
        }
        .product h2 {
            margin: 0;
        }
        .product p {
            margin: 5px 0;
        }
        .product button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .product button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>Votre Panier</h1>

    <div class="product">
        <img src="<?= htmlspecialchars($product['image_link']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <div>
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p>Prix : <?= number_format($product['price'], 2) ?> €</p>
            <p>Quantité disponible : <?= $product['quantity'] ?></p>
            <p>Déjà dans le panier : <?= $alreadyInCart ?></p>
            <p>description : <?= $product['description'] ?></p>
            <form method="POST">
                <input type="hidden" name="product_id" value="<?= $productId ?>">
                <input type="range" name="quantity" min="1" max="<?= $product['quantity'] - $alreadyInCart ?>" value="1">
                <button type="submit" name="add_to_cart">Ajouter au panier</button>
            </form>
        </div>
    </div>

    <a href="cart.php">Voir le panier</a>