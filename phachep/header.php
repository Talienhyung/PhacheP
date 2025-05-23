<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phachep</title>
    <link rel="stylesheet" href="/phachep/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <?php if (!isset($_SESSION["id"])): ?>
                    <li><a href="/phachep/register.php">Register</a></li>
                    <li><a href="/phachep/login.php">Login</a></li>
                <?php else: ?>
                    <li><a href="/phachep/page.php?id=<?= $_SESSION['id'] ?>">Home</a></li>
                    <li><a href="/phachep/cart.php">Cart</a></li>
                    <li><a href="/phachep/article.php">Articles</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>
</html>
