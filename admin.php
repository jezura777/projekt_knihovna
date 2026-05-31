<?php 
require_once "common.php";

$message = "";
$status = false;
$error = false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Správce - Knihovna Divnice</title>
    <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>
    <link rel=stylesheet href="./style.css">
</head>
<body>
    <header>
        <button onclick='window.location.href="index.html"'>Odhlásit se</button>
        <h1>Správce knihovny</h1>
    </header>
    <main>
        <?php if($status): ?>
            <div class="box ok">✓ <?php echo($message); ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="box bad">✗ <?php echo($message); ?></div>
        <?php endif; ?>

        <fieldset class="table margin-block">
            <legend>Přidat Autora</legend>
            <form method="POST" action="add_author.php">
                <label for="author_name">Jméno autora: *</label>
                <input type="text" id="author_name" name="name" required>
                <br>
                <label for="author_born">Narozen:</label>
                <input type="date" id="author_born" name="born">
                <br>
                <label for="author_died">Zemřel:</label>
                <input type="date" id="author_died" name="died">
                <br>
                <button type="submit" class="ok">Přidat autora</button>
            </form>
        </fieldset>

        <fieldset class="table margin-block">
            <legend>Přidat Knihu</legend>
            <form method="POST" action="add_book.php">
                <label for="book_title">Název knihy: *</label>
                <input type="text" id="book_title" name="title" required>
                <br>
                <label for="book_author">Autor: *</label>
                <select id="book_author" name="author_id" required>
                    <option value="" disabled default>Vyberte autora</option>
                    <?php foreach($authors as $author): ?>
                        <option value="<?php echo $author['id']; ?>">
                            <?php echo($author['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <br>
                <label for="book_published">Vydáno:</label>
                <input type="date" id="book_published" name="published">
                <br>
                <label for="book_copies">Počet kopií: *</label>
                <input type="number" id="book_copies" name="copies" value="1" min="1" required>
                <br>
                <button type="submit" class="ok">Přidat knihu</button>
            </form>
        </fieldset>

        <fieldset class="table margin-block">
            <legend>Knihy v knihovně (<?php echo(count($all_books)); ?>)</legend>
            <?php if(count($all_books) > 0): ?>
                <table>
                    <tr>
                        <th>Název</th>
                        <th>Autor</th>
                        <th>Vydáno</th>
                        <th>Kopií</th>
                    </tr>
                    <?php foreach($all_books as $book): ?>
                        <?php 
                            $author = get_by_id($authors, $book['author_id']);
                            $author_name = ($author)? ($author['name']) : 'Neznámý';
                        ?>
                        <tr onclick="window.location.href="update_book.php?id=$book["id"]"">
                            <td><?php echo($book['title']); ?></td>
                            <td><?php echo($author_name); ?></td>
                            <td><?php echo($book['published']); ?></td>
                            <td><?php echo($book['copies']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>V knihovně nejsou žádné knihy.</p>
            <?php endif; ?>
        </fieldset>

        <fieldset class="table margin-block">
            <legend>Autoři (<?php echo(count($authors)); ?>)</legend>
            <?php if(count($authors) > 0): ?>
                <table>
                    <tr>
                        <th>Jméno</th>
                        <th>Narozen</th>
                        <th>Zemřel</th>
                    </tr>
                    <?php foreach($authors as $author): ?>
                        <tr>
                            <td><?php echo($author['name']); ?></td>
                            <td><?php echo(date("d.m.Y", strtotime($author['born']))); ?></td>
                            <td><?php echo(date("d.m.Y", strtotime($author['died']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>V knihovně nejsou žádní autoři.</p>
            <?php endif; ?>
        </fieldset>
        <fieldset class="table margin-block">
            <legend>Uživatelé (<?php echo(count($users)); ?>)</legend>
            <?php if(count($users) > 0): ?>
                    <?php foreach($users as $er): ?>
                            <details>
                            <summary style="display:flex; justify-content:space-between;">
                            <div>
                            <?php echo($er['name']); ?> -
                            <?php echo($er['email']); ?>
                            <?php echo(($er["born"])? "(*".date("d.m.Y", strtotime($er['born'])).")" : ""); ?>
                            </div>
                                <a href="delete_user.php?name=<?php echo($er["name"])?>" class="bad <button>">Vymazat uživatele</a>
                            </summary>
                            <?php 
                                $loaned_render = false;
                                $loaned = '<fieldset class="margin-block table"> <legend>Vypůjčeno</legend> <table>';
                                foreach(["Název", "Vypůjčeno", "Vrátit do"] as $key) {
                                    $loaned = $loaned."<th>".$key."</th>";
                                }
                                $loaned = $loaned."</tr>";
                                foreach($all_loans as $loan) {
                                    $book = get_by_id($all_books, $loan["book_id"]);
                                    if($loan["returned_on"] === NULL && $er["id"] === $loan["user_id"]) {
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
                                    foreach($all_loans as $loan) {
                                        $book = get_by_id($all_books, $loan["book_id"]);
                                        if($loan["returned_on"] !== NULL && $er["id"] === $loan["user_id"]) {
                                            $returned = $returned."<tr><td>".$book["title"]."</td><td>".date("d.m.Y", strtotime($loan["created_at"]))."</td><td>".date("d.m.Y H:i:s", strtotime($loan["returned_on"]))."</td></tr>";
                                            $returned_render = true;
                                        }
                                    }
                                    $returned = $returned."</table> </legend>";
                                    if($returned_render) echo($returned);
                                            ?>
                            </details>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>V knihovně nejsou žádní uživatelé.</p>
            <?php endif; ?>
        </fieldset>
    </main>
</body>
</html>

