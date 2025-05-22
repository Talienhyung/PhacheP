<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP HTML Example</title>
</head>
<body>
    <h1>Welcome to My PHP Page</h1>
    <?php
    try{
        $mySQLClient = new PDO('mysql:host=localhost;dbname=phachepDB', 'root', '');
    }
    catch (PDOException $e){
        die("Connection failed: " . $e->getMessage());
    }
    ?>
</body>