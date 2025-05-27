<?php

$query = $db->prepare("SELECT * FROM User WHERE id = ?");
$query->execute([$_SESSION['id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);
if ($user['role'] !== 'admin') {
    header("Location: page.php?id=" . $_SESSION['id']);
    exit();
}