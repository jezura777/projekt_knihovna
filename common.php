<?php 
require_once "database.php";
$sqlite_fix = (USE_SQLITE)? "rowid AS" : "";
$sqlite_fix2 = (USE_SQLITE)? "rowid" : "id";

$db = db::get();

date_default_timezone_set("Europe/Prague");

$user = [];
$loans = [];
$books = [];
$all_books = [];
$all_loans = [];
$authors = [];
$error=false;
$message = "";
$users = [];

function update() {
    global $user, $loans, $books, $authors, $db, $sqlite_fix, $sqlite_fix2, $error, $message, $all_books, $users, $all_loans;
    try {
        $all_books = $db->query("SELECT ".$sqlite_fix." id,title,author_id,published,copies FROM books");
        $authors = $db->query("SELECT ".$sqlite_fix." id,name,born,died FROM authors");
        $users = $db->query("SELECT ".$sqlite_fix." id,name,born,email FROM users");
        $all_loans = $db->query("SELECT ".$sqlite_fix." id,user_id,book_id,created_at,due_on,returned_on FROM loans");
        // replace the query so that it works with mysql
        if(isset($_GET["name"])) {
            $user = $db->query("SELECT ".$sqlite_fix." id,name,born,email FROM users WHERE name=?", [$_GET["name"]])[0];
            $loans = $db->query("SELECT ".$sqlite_fix." id,user_id,book_id,created_at,due_on,returned_on FROM loans WHERE user_id=?", [$user["id"]]);
            foreach($loans as $loan) {
                $books[] = get_by_id($all_books, $loan["book_id"]);
            }

        }
    }
    catch (PDOException $e) {
        $error=true;
        $message=$e->getMessage();
    }
}

update();

function get_by_id(array $items, int $id): array {
    foreach($items as $item) {
        if($item["id"] === $id) return $item;
    }
    return [];
}
?>
