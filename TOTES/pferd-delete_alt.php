<?php

$servername = "localhost";
$username = "hrppr_1";
$password = "J49Wj7wUbSsKmNC5";
$dbname = "hrppr_db1";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

if ($_POST['connId']) {
    $prepareCon = $conn->prepare('
      DELETE FROM beziehung
      WHERE id_beziehung = ?'
    );
    $bindCon = [$_POST['connId']];
    $prepareCon->execute($bindCon);
}
