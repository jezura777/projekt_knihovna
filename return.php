
<?php 
require_once "common.php";

if($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET["return"])) {
    $ret = (isset($_GET['return']))? (int)$_GET['return'] : null;

    if($ret) {
        try {
            $loan = get_by_id($loans, $ret);
            if ($loan) {
                $db->execute("UPDATE loans SET returned_on=? WHERE ".$sqlite_fix2."=?", [date("Y-m-d H:m:s"),$loan["id"]]);
                $db->execute("UPDATE books SET copies=? WHERE ".$sqlite_fix2."=?", [((int)(get_by_id($all_books, $loan["book_id"])["copies"])+1),$loan["book_id"]]);
                // update copies off the book
                update();
                $status = true;
                $message = "knížka byla úspěšně vrácena.";
            }
            
        } catch (PDOException $e) {
            $error=true;
            $message=$e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Vrátit</title>
        <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>
        <link rel=stylesheet href="./style.css">
    </head>
    <body>
        <header>
            <button onclick='window.location.href="dashboard.php?name=<?php echo($user["name"]) ?>"'>Přehled</button>
            <h1>Jakou knížku dnes vrátíš, <?php echo($user["name"]) ?>?</h1>
        </header>
        <main>
            <?php if ($error) {
                echo('<div id=ret class="warn box">');
                echo('<strong>Chyba</strong>: '.$message);
                echo('</div>');
            }?>
            <?php if ($status) {
                echo('<div id=ret class="ok box">');
                echo($message);
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
                    $book = get_by_id($books, $loan["book_id"]);
                    if($loan["returned_on"] === NULL) {
                        $loaned = $loaned."<tr><td>".$book["title"]."</td><td>".$loan["created_at"]."</td><td>".$loan["due_on"].'</td><td><button class="bad" onclick=\'window.location.href="return.php?name='.$user["name"].'&return='.$loan["id"].'"\'>Vrátit</button></td></tr>';
                        $loaned_render = true;
                    }
                }
                $loaned = $loaned."</table> </section>";
                if($loaned_render) echo($loaned);
                else echo('<h3 class="box info">Nemáte vypůjčené žádné knížky. <a href="./dashboard.php?name='.$user["name"].'">Zpět</a></h3>');
                        ?>
        </main>
    
    </body>
</html>
