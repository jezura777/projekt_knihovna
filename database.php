<?php

$db_sqlite = true;


if(!$db_sqlite) {

    $db_address = 'localhost';
    $db_user = 'root';
    $db_password = ''
    $db_name = 'knihovnadivnice';

    $conn = mysqli_connect($db_address, $db_user, $db_password, $db_name); 
}
else {
    $db_path = './my.db';
    $dsn = "sqlite:$db_path";
    $pdo = new \PDO($dsn);
}


function insert_user($name, $date_of_birth, $email) {
    $sql = "INSERT INTO users (name, date_of_birth, email) VALUES ($name, $date_of_birth, $email);"; 
    if(!$db_sqlite) {
        $vysl = mysqli_query($conn, $sql); 
    }
    else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
}

function insert_author($name, $date_of_birth, $date_of_death) {
    $sql = "INSERT INTO authors (name, date_of_birth, date_of_death) VALUES ($name, $date_of_birth, $date_of_death);"; 
    if(!$db_sqlite) {
        $vysl = mysqli_query($conn, $sql); 
    }
    else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
}
?>
