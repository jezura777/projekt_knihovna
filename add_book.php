<?php 
require_once "common.php";

$message = "";
$error = false;

if($_SERVER["REQUEST_METHOD"] === 'POST') {
    $title = (isset($_POST['title']))? $_POST['title'] : '';
    $author_id = (isset($_POST['author_id'])) ? (int)$_POST['author_id'] : 0;
    $published = (isset($_POST['published'])) ? $_POST['published'] : NULL;
    $copies = (isset($_POST['copies'])) ? (int)$_POST['copies'] : 1;

    if(empty($title)) {
        $error = true;
        $message = "Název knihy je povinný.";
    } elseif($author_id <= 0) {
        $error = true;
        $message = "Musíte vybrat autora.";
    } elseif($copies < 1) {
        $error = true;
        $message = "Počet kopií musí být alespoň 1.";
    } else {
        try {
            $db->execute(
                "INSERT INTO books (title, author_id, published, copies) VALUES (?, ?, ?, ?)",
                [$title, $author_id, $published, $copies]
            );
            $message = "Kniha '{$title}' byla úspěšně přidána.";
            $_SESSION['admin_message'] = $message;
            $_SESSION['admin_status'] = true;
            header('Location: admin.php');
            exit;
        } catch (PDOException $e) {
            $error = true;
            $message = "Chyba při přidávání knihy: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přidat Knihu</title>
    <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>
    <link rel=stylesheet href="./style.css">
</head>
<body>
    <header>
        <button onclick='window.location.href="admin.php"'>Zpět</button>
        <h1>Přidat novou knihu</h1>
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

