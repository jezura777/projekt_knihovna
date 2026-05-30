<?php

require_once "common.php";

echo("{");
echo('"books": ');
echo(json_encode($all_books));
echo(',"authors": ');
echo(json_encode($authors));
echo("}");

?>
