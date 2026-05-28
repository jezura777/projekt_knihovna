<?php
require_once "database.php";
$db = db::get();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $born = $_POST['born'] ?? null;
    $died = $_POST['died'] ?? null;
    
    if ($name) {
        $id = insert_author($db, $name, $born, $died);
        $message = "Author created with ID: $id";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Author</title>
</head>
<body>
    <h1>Insert Author</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" required>
        
        <label>Born:</label>
        <input type="date" name="born">
        
        <label>Died:</label>
        <input type="date" name="died">
        
        <button type="submit">Insert</button>
    </form>
</body>
</html>
