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

$author_id = insert_author("Jezura777", "05-07-2008", null);
    echo("author_id = insert_author(\"Jezura777\", \"05-07-2008\", null) => ". $author_id);

$book_id = insert_book("moje basnicky", "06-04-2026", author_id);
    echo("insert_book(\"moje basnicky\", \"06-04-2026\", author_id) => ". $book_id);

$user_id = insert_user("Lukas Fiala", "05-07-2008", "luki.fi@seznam.cz");
    echo("user_id = insert_user(\"Lukas Fiala\", \"05-07-2008\", \"luki.fi@seznam.cz\") => ". $user_id);

$loan_id = insert_loan($user_id, $book_id, null, null, null);
    echo("loan_id = insert_loan(user_id, book_id, null, null, null) => ". $loan_id);


echo(var_dump(query_smthin('authors')));
echo(var_dump(query_smthin('books')));
echo(var_dump(query_smthin('users')));
echo(var_dump(query_smthin('loans')));

       ?>
    </body>
</html>
