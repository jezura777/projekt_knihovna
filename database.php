<?php

define("USE_SQLITE", true);

define("SQLITE_PATH", __DIR__ . '/my.db');

define('MYSQL_ADDRESS', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_NAME', 'knihovnadivnice');

class db {
    private static ?PDO $instance = null;

    public static function get(): self {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }
        return new self();
    }

    private static function connect(): PDO {
        if (USE_SQLITE) {
            $dsn = 'sqlite:' . SQLITE_PATH;
            $pdo = new PDO($dsn);
            // I'm supposed to do something with enabling foreign keys but I'm too lazy... it will work without it I'm sure
            //
            
        } else {
            $dsn = 'mysql:host=' . MYSQL_ADDRESS . ';dbname=' . MYSQL_NAME . ';charset=utfmb4';
            $pdo = new PDO($dsn, MYSQL_USER, MYSQL_PASSWORD);
        }

        // some error exception attribute and associative array bullshit that I won't do cause I'm not an AI
        // ... okay will do but only this one. The second one is usseless...
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // ....... the second one is actually pretty usefull well I think I better set it ._.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        return $pdo;
    }


    // ******* functions for raw sqlign :3 ********
    public function query(string $sql, array $params = []): array {
        $stmt = self::$instance->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): int {
        $stmt = self::$instance->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function last_id(): string {
        return self::$instance->lastInsertId();
    }

}


function query_smthin(db $db, string $query_whaat): array {
    if($query_whaat === 'books') {
        return $db->query("
            SELECT b.*, a.name AS author_name
            FROM   books b
            LEFT JOIN authors a ON a.id = b.author_id
            ORDER BY b.title
        ");
    } elseif ($query_whaat === 'loans') {
        return $db->query("
            SELECT l.*,
                   u.name  AS user_name,
                   b.title AS book_title
            FROM   loans l
            JOIN   users u ON u.id = l.user_id
            JOIN   books b ON b.id = l.book_id
            ORDER BY l.loaned_on DESC
        ");
    } else {
        return $db->query("SELECT * FROM " . $query_whaat . " ORDER BY name");
    }
}

// TODO: make one insert function with var_args
// 

/*function insert(string table, array params = []): int {
    $description = [
        'authors' => ['name', 'born', 'died'],
        'books' => ['title', 'published', 'author_id', 'copies'],
        'users' => ['name', 'born', 'email']
    ];
}*/

function insert_author(db $db, string $name, ?string $born, ?string $died): int {
    $db->execute(
        "INSERT INTO authors (name, born, died) VALUES (?, ?, ?)",
        [$name, $born, $died]
    );
    return (int)$db->last_id();
}

function insert_book(db $db, string $title, ?string $published, int $author_id, int $copies = 1): int {
    $db->execute(
        "INSERT INTO books (titLe, published, author_id, copies) VALUES (?, ?, ?, ?)",
        [$title, $published, $author_id, $copies]
    );
    return (int)$db->last_id();
}

function insert_user(db $db, string $name, ?string $born, ?string $email): int {
    $db->execute(
        "INSERT INTO users (name, born, email) VALUES (?, ?, ?)",
        [$name, $born, $email]
    );
    return (int)$db->last_id();
}

function insert_loan(db $db, int $user_id, int $book_id, ?string $processed_at, ?string $loaned_on, ?string $due_on): int {
    $db->execute(
        "INSERT INTO loans (user_id, book_id, processed_at, loaned_on, due_on) VALUES (?, ?, ?, ?, ?)",
        [$user_id, $book_id, $processed_at, $loaned_on, $due_on]
    );
    return (int)$db->last_id();
}


function delete_author(db $db, int $id) {
    $db->execute(
        "DELETE FROM authors WHERE id=?",
        [$id]
    );
}

function delete_book(db $db, int $id) {
    $db->execute(
        "DELETE FROM books WHERE id=?",
        [$id]
    );
}

function delete_user(db $db, int $id) {
    $db->execute(
        "DELETE FROM users WHERE id=?",
        [$id]
    );
}

function delete_loan(db $db, int $id) {
    $db->execute(
        "DELETE FROM loans WHERE id=?",
        [$id]
    );
}


function update_author(db $db, int $id, string $name, ?string $born, ?string $died): int {
    $db->execute(
        "UPDATE authors name=?, born=?, died=? WHERE id=?",
        [$name, $born, $died, $id]
    );
    return (int)$db->last_id();
}

function update_book(db $db, int $id, string $title, ?string $published, int $author_id, int $copies = 1): int {
    $db->execute(
        "UPDATE books (title=?, published=?, author_id=?, copies=? WHERE id=?",
        [$title, $published, $author_id, $copies, $id]
    );
    return (int)$db->last_id();
}

function update_user(db $db, int $id, string $name, ?string $born, ?string $email): int {
    $db->execute(
        "UPDATE users name=?, born=?, email=? WHERE id=?",
        [$name, $born, $email, $id]
    );
    return (int)$db->last_id();
}

function update_loan(db $db, int $id, int $user_id, int $book_id, ?string $processed_at, ?string $loaned_on, ?string $due_on): int {
    $db->execute(
        "UPDATE loans SET user_id=?, book_id=?, processed_at=?, loaned_on=?, due_on=? WHERE id=?",
        [$user_id, $book_id, $processed_at, $loaned_on, $due_on, $id]
    );
    return (int)$db->last_id();
}

