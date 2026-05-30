
<?php 
require_once "common.php";

$status = false;

if($_SERVER["REQUEST_METHOD"] === 'GET' && isset($_GET["loan"])) {
    $loan = (isset($_GET['loan']))? (int)$_GET['loan'] : null;

    if($loan) {
        try {
            $book = get_by_id($all_books, $loan);
            if ($book) {
                $db->execute("INSERT INTO loans (user_id, book_id, due_on) VALUES (?,?,?)", [$user["id"], $book["id"], date("Y-m-d")]);
                $db->execute("UPDATE books SET copies=? WHERE ".$sqlite_fix2."=?", [((int)($book["copies"])-1),$book["id"]]);
                update();
                $status = true;
                $message = "Knížka byla úspěšně vypůjčena.";
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
        <title>Vypůjčit</title>
        <link rel=stylesheet href=https://unpkg.com/missing.css@1.3.0>
        <link rel=stylesheet href="./style.css">
    </head>
    <body onload="get_books()">
        <header>
            <button onclick='window.location.href="dashboard.php?name=<?php echo($user["name"]) ?>"'>Přehled</button>
            <h1>Jakou knížku si dnes vypůjčíš, <?php echo($user["name"]) ?>?</h1>
        </header>
        <main>
            <?php if ($error) {
                echo('<div id=ret class="warn box">');
                echo('<strong>Chyba</strong>: '.$message);
                echo('</div>');
            }?>
            <?php if ($status) {
                echo('<div id=ret class="info box">');
                echo($message);
                echo('</div>');
            }?>

            <div id="container">
            </div>
            <div id="error" style="visibility:hidden;" class="box bad"><h3>Momentálně nejsou tyto knížky dostupné nebo nebyly nalezeny. </h3><p>Zkuste zadat jiný termín.</p></div>
        </main>
    
    </body>

<script>

function search_objects(list, query) {  
    const q = query.toLowerCase();  
    return list.filter(obj => JSON.stringify(Object.values(obj)).toLowerCase().includes(q));
}

tbl = document.createElement("table");
search = document.createElement("input");
search.type = "text";
search.placeholder = "Vyhledejte knížku...";

search.addEventListener('input', function(e) {

    render_search_table(search_objects(data["books"], search.value), data);
});

br = document.createElement("br");
container.appendChild(search);
container.appendChild(br);
container.appendChild(tbl);

function render_search_table(books, data) {
    render = false;
    authors = data["authors"];
    authors.sort((a1,a2) => {return a1["id"]-a2["id"]});
    html = "";

    html += `<tr><th>Název</th><th>Autor</th><th>Vydáno</th><th>Počet</th></tr>`;
    books.forEach(book => {
        book["author_name"] = authors[Number(book["author_id"])-1]["name"];


        if(Number(book["copies"]) > 0) {
            html += `<tr><td>${book["title"]}</td><td>${book["author_name"]}</td><td>${book["published"]}</td><td>${book["copies"]}</td><td><a class="<button> ok" href="${window.location.href + `&loan=${book["id"]}`}">Vypůjčit</a></td></tr>`;
            render = true;
        }
    });

    if(render) {
        err.style.visibility = "hidden";
        tbl.innerHTML = html;
    } else {
        err.style.visibility = "visible";
        tbl.innerHTML = "";
        return;
    }
}

async function get_books() {
    container = document.getElementById("container");
    err = document.getElementById("error");

    try {
        data = await fetch('/data.php').then(response => {return response.json();});
        render_search_table(data["books"], data);

    } catch (err) {
        console.error('Request failed', err);
    }
}
</script>



</html>
