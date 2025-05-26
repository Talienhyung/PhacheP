<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phachep</title>
    <link rel="stylesheet" href="/phachep/styles.css">
    <style>
        header {
            background-color: #1a1a1a;
            color: white;
            padding: 1rem;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 1rem;
            margin: 0;
            padding: 0;
        }
        nav a {
            color: white;
            text-decoration: none;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .nav-left, .nav-right {
            display: flex;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="nav-left">
                <a href="index.php"><strong>Phachep</strong></a>
                <?php if (isset($_SESSION["id"])): ?>
                    <a href="/phachep/page.php?id=<?= $_SESSION['id'] ?>">Mon compte</a>
                    <a href="/phachep/cart.php">Panier</a>
                    <a href="/phachep/article.php">Nouveau article</a>
                <?php endif; ?>
            </div>

            <div class="nav-right">
                <?php if (!isset($_SESSION["id"])): ?>
                    <a href="/phachep/register.php">Inscription</a>
                    <a href="/phachep/login.php">Connexion</a>
                <?php else: ?>
                    <a href="/phachep/logout.php">Déconnexion</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
</body>
</html>
