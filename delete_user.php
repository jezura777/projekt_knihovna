<?php 
require_once "common.php";

$message = "";
$error = false;

if($_SERVER["REQUEST_METHOD"] === 'GET') {
    $name = (isset($_GET['name']))? $_GET['name'] : NULL;

    if(!$name) {
        $error = true;
        $message = "Nepodařilo se vymazat uživatele.";
    } else {
        try {
            $active_loans = [];
            foreach($loans as $loan) {
                if($loan["returned_on"] === NULL){
                    $active_loans[] = $loan;
                }
            }
            if(count($active_loans) < 1) {
                $db->execute( "DELETE FROM users WHERE ".$sqlite_fix2."=?", [$user["id"]]);
                $message = "Uživatel '{$name}' byl úspěšně odebrán.";
            }
            else {
                $message = "Nejdřív je nutno vrátit všechy vypůjčené knížky!";
                $error = true;
                $back = "dashboard.php?name=".$name;
            }
        } catch (PDOException $e) {
            $error = true;
            $message = "Chyba při přidávání knihy: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vymazat uživatele</title>
    <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>
    <link rel=stylesheet href="./style.css">
</head>
<body>
    <header>
    <button onclick='window.location.href="<?php echo(($back)? $back:"index.html")?>"'>Zpět</button>
        <h1>Vymazat uživatele</h1>
    </header>
    <main>
        <?php if($error): ?>
            <div class="warn box">
                <strong>Chyba</strong>: <?php echo($message); ?>
            </div>
        <?php else:?>
            <div class="box ok">
                <?php echo($message); ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

