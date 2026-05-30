
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
        <link rel=stylesheet href="./style.css">
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
                    <a class="<button> ok" href="return.php?name=<?php echo($user["name"])?>">Vrátit</a>
                </div>
                <div class="loan">
                    <a class="<button> ok" href="loan.php?name=<?php echo($user["name"])?>">Vypůjčit</a>
                </div>
                </div>
            </section>
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
                        $loaned = $loaned."<tr><td>".$book["title"]."</td><td>".$loan["created_at"]."</td><td>".$loan["due_on"]."</td></tr>";
                        $loaned_render = true;
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
                    $book = get_by_id($books, $loan["book_id"]);
                    if($loan["returned_on"] !== NULL) {
                        $returned = $returned."<tr><td>".$book["title"]."</td><td>".$loan["created_at"]."</td><td>".$loan["returned_on"]."</td></tr>";
                        $returned_render = true;
                    }
                }
                $returned = $returned."</table> </section>";
                if($returned_render) echo($returned);
                        ?>
        </main>
    
    </body>
</html>
