
<?php

require_once "database.php";
$db = db::get();

$error = false;
if($_SERVER["REQUEST_METHOD"] === 'POST') {
    //echo("hello");
    $visible = true;
    $name = (isset($_POST['username']))? $_POST['username'] : null;
    $born = (isset($_POST['born']))? $_POST['born'] : null;
    $email = (isset($_POST['email']))? $_POST['email'] : null;

    //echo("<br>kamarad $name,  $born,  $email");
    if($name && $email) {
        try {
            insert_user($db, $name, $born, $email);
     //       echo("insert_user($db, $name, $born, $email);");
        } catch (PDOException $e) {
            $error = true;
            $message = $e->getMessage();
        }
    }
}
else {
    $visible = false;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knihovna Divnice: Registrace</title>
    <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>

<style>
.w100 {
width:100% !important; 
}
</style>

</head>
<body>
    <header>
            <button class="info" onclick='window.location.href="index.html"'>Zpět</button>
    </header>
    <main>
        <form id="form" method="post">
        <label for="username">Jméno:</label>
        <input type="text" id="username" class="w100" name="username">
        <label for="email">e-mail:</label>
        <input type="text" id="email" class="w100" name="email">
        <label for="born">Datum narození:</label>
        <input type="date" class="w100" name="born">
        <br>
        <button type="submit" id="register">Registrovat</button>
        </form>
        <?php 
            if($error) {
                echo('<div id=ret class="warn box">');
                echo("<p><strong>Chyba</strong>: ".$message."</p>");
                echo("</div>");
            } else if($visible) {
                echo('<div id=ret class="ok box">');
                echo('<p><strong>Registrace proběhla úspěšně můžete se přihlásit</strong>: <a href="index.html">Zpět</a></p>');
                echo("</div>");
            }
        ?>
    </main>
</body>

<script>
    let form = document.getElementById("form");
    let email = document.getElementById("email");
    let username = document.getElementById("username");

    form.addEventListener("input", function (e) {
        if(email.value == "" || username.value == "") {
            document.getElementById("register").classList.remove("ok");
        } else {
            document.getElementById("register").classList.add("ok");
        }
    });
</script>

</html>

