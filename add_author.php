<?php 
require_once "common.php";

$message = "";
$error = false;

if($_SERVER["REQUEST_METHOD"] === 'POST') {
    $name = (isset($_POST['name']))? $_POST['name'] : '';
    $born = (isset($_POST['born']))? $_POST['born'] : NULL;
    $died = (isset($_POST['died']))? $_POST['died'] : NULL;

    if(empty($name)) {
        $error = true;
        $message = "Jméno autora je povinné.";
    } else {
        try {
            $db->execute(
                "INSERT INTO authors (name, born, died) VALUES (?, ?, ?)",
                [$name, $born, $died]
            );
            $message = "Autor '{$name}' byl úspěšně přidán.";
            $_SESSION['admin_message'] = $message;
            $_SESSION['admin_status'] = true;
            header('Location: admin.php');
            exit;
        } catch (PDOException $e) {
            $error = true;
            $message = "Chyba při přidávání autora: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přidat Autora</title>
    <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>
    <link rel=stylesheet href="./style.css">
</head>
<body>
    <header>
        <button onclick='window.location.href="admin.php"'>Zpět</button>
        <h1>Přidat nového autora</h1>
    </header>
    <main>
        <?php if($error): ?>
            <div class="warn box">
                <strong>Chyba</strong>: <?php echo($message); ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

