<?php 
require_once "database.php";
$sqlite_fix = (USE_SQLITE)? "rowid AS" : "";
$sqlite_fix2 = (USE_SQLITE)? "rowid" : "id";

$db = db::get();

$error=false;
try {
    // replace the query so that it works with mysql
    if(isset($_GET["name"])) {
        $user = $db->query("SELECT ".$sqlite_fix." id,name,born,email FROM users WHERE name=?", [$_GET["name"]])[0];
        $loans = $db->query("SELECT ".$sqlite_fix." id,user_id,book_id,created_at,due_on,returned_on FROM loans WHERE user_id=?", [$user["id"]]);
        $books = [];
        foreach($loans as $loan) {
            $books[] = $db->query("SELECT ".$sqlite_fix." id,title,author_id,published,copies FROM books WHERE ".$sqlite_fix2."=?", [$loan["book_id"]])[0];
        }

        $authors = [];
        foreach($books as $book) {
            $authors[] = $db->query("SELECT ".$sqlite_fix." id,name,born,died FROM authors WHERE ".$sqlite_fix2."=?", [$book["author_id"]]);
        }
    }
}
catch (PDOException $e) {
    $error=true;
    $message=$e->getMessage();
}

function get_book(array $books, int $id): array {
    foreach($books as $book) {
        if($book["id"] === $id) return $book;
    }
}
function get_author(array $authors, int $id): array {
    foreach($authors as $author) {
        if($author["id"] === $id) return $author;
    }
}
?>