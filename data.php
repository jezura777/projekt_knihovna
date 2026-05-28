<?php
require_once "database.php";
$db = db::get();

$tables = ['authors', 'books'];
if(isset($_GET["supersecretkey"])) {
    if($_GET["supersecretkey"] == "password1234") {
        $tables = ['authors', 'books', 'users', 'loans'];
    }
}

echo('{');
foreach($tables as $table){
    echo('"'. $table .'": ');
    echo(json_encode(query_smthin($db, $table)) . "<br>");
    echo(',');
}
echo('}');
?>
