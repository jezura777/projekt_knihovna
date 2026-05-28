<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        <?php
require_once 'database.php';
$db = db::get();

$author_id = insert_author($db, "Jezura777", "05-07-2008", null);
    echo("author_id = insert_author(\"Jezura777\", \"05-07-2008\", null) => ". $author_id. "<br>");

$book_id = insert_book($db, "moje basnicky", "06-04-2026", $author_id);
    echo("insert_book(\"moje basnicky\", \"06-04-2026\", author_id) => ". $book_id. "<br>");

$user_id = insert_user($db, "Lukas Fiala", "05-07-2008", "luki.fi@seznam.cz");
    echo("user_id = insert_user(\"Lukas Fiala\", \"05-07-2008\", \"luki.fi@seznam.cz\") => ". $user_id. "<br>");

$loan_id = insert_loan($db, $user_id, $book_id, null, null, null);
    echo("loan_id = insert_loan(user_id, book_id, null, null, null) => ". $loan_id. "<br>");


echo(var_dump(query_smthin($db, 'authors')) . "<br>");
echo(var_dump(query_smthin($db, 'books')). "<br>");
echo(var_dump(query_smthin($db, 'users')). "<br>");
echo(var_dump(query_smthin($db, 'loans')). "<br>");

       ?>
    </body>
</html>
