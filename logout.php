<?php
session_start();
session_unset();
session_destroy();
header("Location: /phachep/login.php");
exit();
