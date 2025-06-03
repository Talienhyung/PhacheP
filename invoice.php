<?php
include_once "header.php";
require_once "auth.php";

require_once 'db_config.php';
try {
    $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
} catch (PDOException $e) {
    die("Connexion échouée, ô majesté : " . $e->getMessage());
}


$invoiceId = $_GET["id"];
$query = $db->prepare("SELECT * FROM Invoice WHERE id = ?");
$query->execute([$invoiceId]);
$invoice = $query->fetch(PDO::FETCH_ASSOC);
if (!$invoice) {
    echo "<p style='color:red;'>Facture introuvable... </p>";
    exit();
}

// Affichage des données de la facture
echo "<h2>Détails de la facture #" . htmlspecialchars($invoice['id']) . "</h2>";
echo "<ul>";
echo "<li><strong>Date de transaction :</strong> " . htmlspecialchars($invoice['transaction_date']) . "</li>";
echo "<li><strong>Montant :</strong> " . htmlspecialchars($invoice['amount']) . " €</li>";
echo "<li><strong>Adresse de facturation :</strong> " . htmlspecialchars($invoice['billing_address']) . "</li>";
echo "<li><strong>Ville :</strong> " . htmlspecialchars($invoice['billing_city']) . "</li>";
echo "<li><strong>Code postal :</strong> " . htmlspecialchars($invoice['billing_zipcode']) . "</li>";
echo "</ul>";

?>

