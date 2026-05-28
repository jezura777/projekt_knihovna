<?php
require_once 'database.php';

$db = db::get();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? null;
    $published = $_POST['published'] ?? null;
    $author_id = $_POST['author_id'] ?? null;
    $copies = $_POST['copies'] ?? 1;
    
    if ($title && $author_id) {
        $id = insert_book($db, $title, $published, (int)$author_id, (int)$copies);
        $message = "Book created with ID: $id";
    }
}

$authors = query_smthin($db, 'authors');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Book</title>
</head>
<body>
    <h1>Insert Book</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" required>
        
        <label>Published:</label>
        <input type="date" name="published">
        
        <label>Author:</label>
        <select name="author_id" required>
            <option value="">Select Author</option>
            <?php foreach ($authors as $author): ?>
                <option value="<?= $author['id'] ?>"><?= htmlspecialchars($author['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Copies:</label>
        <input type="number" name="copies" value="1">
        
        <button type="submit">Insert</button>
    </form>
</body>
</html>

