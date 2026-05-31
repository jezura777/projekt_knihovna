
<?php 
// TODO: remove user
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
            <div style="display: flex;flex-direction: row;justify-content: space-between">
            <button onclick='window.location.href="index.html"'>Odhlásit</button>
            <button onclick='decide()' style="position:relative; right:0;"class="bad align-self:end" >Vymazat účet</button>
            </div>
            <h1>Co si přečteš dnes, <?php echo($user["name"]) ?>?</h1>
        </header>
        <main>
            <?php if ($error) {
                echo('<div id=ret class="warn box" style="visibility:hidden">');
                echo('<strong>Chyba</strong>: '.$message);
                echo('</div>');
            }?>
            <fieldset class="margin-block table">
                <legend>Rychlé akce</legend>
                <div class="row">
                <div class="return">
                    <a class="<button> ok" href="return.php?name=<?php echo($user["name"])?>">Vrátit</a>
                </div>
                <div class="loan">
                    <a class="<button> ok" href="loan.php?name=<?php echo($user["name"])?>">Vypůjčit</a>
                </div>
                </div>
            </fieldset>
            <?php 
                $loaned_render = false;
                $loaned = '<fieldset class="margin-block table"> <legend>Vypůjčeno</legend> <table>';
                foreach(["Název", "Vypůjčeno", "Vrátit do"] as $key) {
                    $loaned = $loaned."<th>".$key."</th>";
                }
                $loaned = $loaned."</tr>";
                foreach($loans as $loan) {
                    $book = get_by_id($books, $loan["book_id"]);
                    if($loan["returned_on"] === NULL) {
                        $red = ((date("Y-m-d") > $loan["due_on"])? "bad" : ((date("Y-m-d", strtotime("+3 days")) > $loan["due_on"])? "warn" : ""));
                        $loaned = $loaned."<tr class=".$red." style=\"background: var(--box-bg);\"><td>".$book["title"]."</td><td>".date("d.m.Y H:i:s", strtotime($loan["created_at"]))."</td><td>".date("d.m.Y", strtotime($loan["due_on"]))."</td></tr>";
                        $loaned_render = true;
                    }
                }
                $loaned = $loaned."</table> </fieldset>";
                if($loaned_render) echo($loaned);
                        ?>
            <?php 
                $returned_render = false;
                $returned = '<fieldset class="margin-block table"> <legend>Vrácené</legend> <table> ';
                foreach(["Název", "Vypůjčeno", "Vráceno"] as $key) {
                    $returned = $returned."<th>".$key."</th>";
                }
                $returned = $returned."</tr>";
                $loans = array_reverse($loans);
                foreach($loans as $loan) {
                    $book = get_by_id($books, $loan["book_id"]);
                    if($loan["returned_on"] !== NULL) {
                        $returned = $returned."<tr><td>".$book["title"]."</td><td>".date("d.m.Y", strtotime($loan["created_at"]))."</td><td>".date("d.m.Y H:i:s", strtotime($loan["returned_on"]))."</td></tr>";
                        $returned_render = true;
                    }
                }
                $returned = $returned."</table> </legend>";
                if($returned_render) echo($returned);
                        ?>
        </main>
    
    </body>
<script>
function decide() {
    if (confirm('Chcete doopravdy vymazat Váš účet?')) {
        window.location.href="delete_user.php?name=<?php echo($user["name"])?>"
    } else {
        alert("Sem si jako myslel!")
    }
}
</script>
</html>
