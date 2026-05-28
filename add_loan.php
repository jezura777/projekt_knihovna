<?php
require_once 'database.php';

$db = db::get();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $book_id = $_POST['book_id'] ?? null;
    $processed_at = $_POST['processed_at'] ?? null;
    $loaned_on = $_POST['loaned_on'] ?? null;
    $due_on = $_POST['due_on'] ?? null;
    
    if ($user_id && $book_id) {
        $id = insert_loan($db, (int)$user_id, (int)$book_id, $processed_at, $loaned_on, $due_on);
        $message = "Loan created with ID: $id";
    }
}

$users = query_smthin($db, 'users');
$books = query_smthin($db, 'books');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Loan</title>
</head>
<body>
    <h1>Insert Loan</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label>User:</label>
        <select name="user_id" required>
            <option value="">Select User</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Book:</label>
        <select name="book_id" required>
            <option value="">Select Book</option>
            <?php foreach ($books as $book): ?>
                <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label>Processed At:</label>
        <input type="datetime-local" name="processed_at">
        
        <label>Loaned On:</label>
        <input type="datetime-local" name="loaned_on">
        
        <label>Due On:</label>
        <input type="date" name="due_on">
        
        <button type="submit">Insert</button>
    </form>
</body>
</html>

