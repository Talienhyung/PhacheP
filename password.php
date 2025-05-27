<?php
$word = "password";
$hash = password_hash($word, PASSWORD_DEFAULT);
echo "Mot de passe : $hash\n";