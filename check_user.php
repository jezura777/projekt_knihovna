<?php

require_once "database.php";
$db = db::get();

header('Content-Type: text/plain; charset=utf-8');

$name = isset($_GET['name']) ? trim($_GET['name']) : '';

$error = false;
if ($name !== '') {
    try {
        $user = $db->query("SELECT * FROM users WHERE name=?", [$name]);
        if (!$user) $error = true;
    } catch (PDOException $e) {
        $error = true;
    }

}

echo $error ? 'true' : 'false';


?>
