
<?php 
require_once "database.php";

$db = db::get();

$error=false;
try {
    // replace the query so that it works with mysql
    if(isset($_GET["name"])) {
        $user = $db->query("SELECT rowid AS id,name,born,email FROM users WHERE name=?", [$_GET["name"]])[0];
        $loans = $db->query("SELECT rowid AS id,user_id,book_id,created_at,due_on,returned_on FROM loans WHERE user_id=?", [$user["id"]]);
        $books = [];
        foreach($loans as $loan) {
            $books[] = $db->query("SELECT rowid AS id,title,author_id,published,copies FROM books WHERE rowid=?", [$loan["book_id"]])[0];
        }

        $authors = [];
        foreach($books as $book) {
            $authors[] = $db->query("SELECT rowid AS id,name,born,died FROM authors WHERE rowid=?", [$book["author_id"]]);
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

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Můj přehled</title>
        <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>
    </head>
    <body>
        <header>
            <button onclick='window.location.href="dashboard.php?name=<?php echo($user["name"]) ?>"'>Přehled</button>
            <h1>Jakou knížku dnes vrátíš, <?php echo($user["name"]) ?>?</h1>
        </header>
        <main>
            <?php if ($error) {
                echo('<div id=ret class="warn box" style="visibility:hidden">');
                echo('<strong>Chyba</strong>: '.$message);
                echo('</div>');
            }?>
            <?php 
                $loaned_render = false;
                $loaned = '<section class="returned"> <h2>Vypůjčeno</h2> <table>';
                foreach(["Název", "Vypůjčeno", "Vrátit do"] as $key) {
                    $loaned = $loaned."<th>".$key."</th>";
                }
                $loaned = $loaned."</tr>";
                foreach($loans as $loan) {
                    $book = get_book($books, $loan["book_id"]);
                    if($loan["returned_on"] === NULL) {
                        $loaned = $loaned."<tr><td>".$book["title"]."</td><td>".$loan["created_at"]."</td><td>".$loan["due_on"].'</td><td><button class="bad" onclick=\'window.location.href="return.php?name='.$user["name"].'&return='.$loan["id"].'"\'>Vrátit</button></td></tr>';
                        $loaned_render = true;
                        break;
                    }
                }
                $loaned = $loaned."</table> </section>";
                if($loaned_render) echo($loaned);
                        ?>
        </main>
    
    </body>
</html>
