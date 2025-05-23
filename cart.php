<?php
require_once "auth.php";

try {
    $db = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}

$userId = $_SESSION["id"];

// Mise à jour si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id'], $_POST['new_quantity'])) {
    $articleId = (int) $_POST['article_id'];
    $newQty = max(0, (int) $_POST['new_quantity']); // éviter valeurs négatives

    // Supprimer les anciennes lignes
    $deleteStmt = $db->prepare("DELETE FROM Cart WHERE user_id = ? AND article_id = ?");
    $deleteStmt->execute([$userId, $articleId]);

    // Réinsérer selon la nouvelle quantité
    if ($newQty > 0) {
        $insertStmt = $db->prepare("INSERT INTO Cart (user_id, article_id) VALUES (?, ?)");
        for ($i = 0; $i < $newQty; $i++) {
            $insertStmt->execute([$userId, $articleId]);
        }
    }
    // Redirection pour éviter la résubmission
    header("Location: cart.php");
    exit();
}

// Récupération des articles dans le panier
$query = $db->prepare("SELECT username, email, balance, profile_picture, role FROM User WHERE id = ?");
$query->execute([$userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT 
        A.id AS article_id,
        A.name,
        A.price,
        COUNT(C.id) AS quantity
    FROM Cart C
    JOIN Article A ON C.article_id = A.id
    WHERE C.user_id = ?
    GROUP BY A.id, A.name, A.price
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll();
?>

<h1>Votre panier</h1>
<ul>
<?php foreach ($cartItems as $item): ?>
    <li>
        <form method="POST" style="display: inline;">
            <?= htmlspecialchars($item['name']) ?> –
            <?= number_format($item['price'], 2) ?> €
            × 
            <input type="number" name="new_quantity" value="<?= $item['quantity'] ?>" min="0" style="width: 50px;">
            <input type="hidden" name="article_id" value="<?= $item['article_id'] ?>">
            <button type="submit">Mettre à jour</button>
        </form>
        = <strong><?= number_format($item['price'] * $item['quantity'], 2) ?> €</strong>
    </li>
<?php endforeach; ?>
</ul>

<p>
    <strong>Total : 
    <?php
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        echo number_format($total, 2);
    ?> €</strong>
</p>
