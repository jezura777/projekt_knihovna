
<?php 
require_once "common.php";
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
