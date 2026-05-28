
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
            <button onclick='window.location.href="index.html"'>Odhlásit</button>
            <h1>Co si přečteš dnes, <?php echo($user["name"]) ?>?</h1>
        </header>
        <main>
            <?php if ($error) {
                echo('<div id=ret class="warn box" style="visibility:hidden">');
                echo('<strong>Chyba</strong>: '.$message);
                echo('</div>');
            }?>
            <section class="quick"> 
                <h2>Rychlé akce</h2>
                <div class="row">
                <div class="return">
                    <a class="<button> ok" href="return.php">Vrátit</a>
                </div>
                <div class="loan">
                    <a class="<button> ok" href="loan.php">Vypůjčit</a>
                </div>
                </div>
            </section>
            <?php 
                $loaned_render = false;
                $loaned = '<section class="returned"> <h2>Vrácené</h2> <table>';
                foreach(["Název", "Vypůjčeno", "Vrátit do"] as $key) {
                    $loaned = $loaned."<th>".$key."</th>";
                }
                $loaned = $loaned."</tr>";
                foreach($loans as $loan) {
                    $book = get_book($books, $loan["book_id"]);
                    if($loan["returned_on"] === NULL) {
                        $loaned = $loaned."<tr><td>".$book["title"]."</td><td>".$loan["created_at"]."</td><td>".$loan["due_on"]."</td></tr>";
                        $loaned_render = true;
                        break;
                    }
                }
                $loaned = $loaned."</table> </section>";
                if($loaned_render) echo($loaned);
                        ?>
            <?php 
                $returned_render = false;
                $returned = '<section class="returned"> <h2>Vrácené</h2> <table> ';
                foreach(["Název", "Vypůjčeno", "Vráceno"] as $key) {
                    $returned = $returned."<th>".$key."</th>";
                }
                $returned = $returned."</tr>";
                foreach($loans as $loan) {
                    $book = get_book($books, $loan["book_id"]);
                    if($loan["returned_on"] !== NULL) {
                        $returned = $returned."<tr><td>".$book["title"]."</td><td>".$loan["created_at"]."</td><td>".$loan["returned_on"]."</td></tr>";
                        $returned_render = true;
                        break;
                    }
                }
                $returned = $returned."</table> </section>";
                if($returned_render) echo($returned);
                        ?>
        </main>
    
    </body>
</html>
