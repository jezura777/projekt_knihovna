
<?php 
require_once "common.php";

define("FINE", 50);

$alert = "";

if($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET["return"])) {
    $ret = (isset($_GET['return']))? (int)$_GET['return'] : null;

    if($ret) {
        try {
            $loan = get_by_id($loans, $ret);
            if ($loan) {
                if(date("Y-m-d",strtotime($loan["due_on"])) < date("Y-m-d")) {
                    $alert = "Zaplaťte pokutu za pozdní vrácení: ".number_format(((time() - strtotime($loan["due_on"]))/(60*60*24))*FINE, 2) ."kč!";
                }
                $db->execute("UPDATE loans SET returned_on=? WHERE ".$sqlite_fix2."=?", [date("Y-m-d H:i:s"),$loan["id"]]);
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
<script>
if("<?php echo($alert)?>" !== "") alert("<?php echo($alert)?>")
</script>
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
                $loaned = '<fieldset class="margin-block table"> <legend>Vypůjčeno</legend> <table>';
                foreach(["Název", "Vypůjčeno", "Vrátit do", ""] as $key) {
                    $loaned = $loaned."<th>".$key."</th>";
                }
                $loaned = $loaned."</tr>";
                foreach($loans as $loan) {
                    $book = get_by_id($books, $loan["book_id"]);
                    if($loan["returned_on"] === NULL) {
                        $red = ((date("Y-m-d") > $loan["due_on"])? "bad" : ((date("Y-m-d", strtotime("+3 days")) > $loan["due_on"])? "warn" : ""));
                        $loaned = $loaned."<tr class=".$red." style=\"background: var(--box-bg);\"><td>".$book["title"]."</td><td>".date("d.m.Y H:i:s", strtotime($loan["created_at"]))."</td><td>".date("d.m.Y", strtotime($loan["due_on"])).'</td><td><button class="bad" onclick=\'window.location.href="return.php?name='.$user["name"].'&return='.$loan["id"].'"\'>Vrátit</button></td></tr>';
                        $loaned_render = true;
                    }
                }
                $loaned = $loaned."</table> </fieldset>";
                if($loaned_render) echo($loaned);
                        ?>
        </main>
    
    </body>
</html>
