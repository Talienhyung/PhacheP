<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP HTML Example</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Welcome to My PHP Page</h1>

    <?php
    try {
        $mySQLClient = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
        $mySQLClient->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour récupérer tous les utilisateurs
        $query = $mySQLClient->query('SELECT id, username, email, balance, role FROM `User`');
        $users = $query->fetchAll(PDO::FETCH_ASSOC);

        if (count($users) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Balance</th></tr>";

            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['balance']) . " €</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p style='text-align:center;'>Aucun utilisateur trouvé dans la base de données.</p>";
        }
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    ?>

    <p style="text-align:center; margin-top: 30px;">
    <a href="/phachep/register.php" style="display:inline-block; background:#333; color:white; padding:10px 20px; border-radius:5px; text-decoration:none;">
        ➕ Créer un compte
    </a>
</p>

</body>
</html>