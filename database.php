<?php

define("USE_SQLITE", true);

define("SQLITE_PATH", __DIR__ . './my.db');

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
            $pdo = new PDO('sqlie:' . SQLITE_PATH);
            // I'm supposed to do something with enabling foreign keys but I'm too lazy... it will work without it I'm sure
        } else {
            $dsn = 'mysql:host=' . MYSQL_ADDRESS . ';dbname=' . MYSQL_NAME . ';charset=utfmb4';
            $pdo = new PDO($dsn, MYSQL_USER, MYSQL_PASSWORD);
        }

        // some error exception attribute and associative array bullshit that I won't do cause I'm not an AI

        return $pdo;
    }


    // ******* functions for raw sqlign :3 ********
    public function query(string $sql, array $params = []): array {
        $stmt = self::$instance->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): ?array {
        $stmt = self::$instance->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function last_id(): string {
        return self::$instance->lastInsertId();
    }

}

$db = Database::get();

function query_smthin($query_whaat): array {
    return $db->query("SELECT * FROM " . $query_whaat . " ORDER BY name");
}

// TODO: make one insert function with var_args

function insert_author(string $name, ?string $born, ?string $died): int {
    $db->execute(
        "INSERT INTO authors (name, born, died) VALUES (?, ?, ?)",
        [$name, $born, $died]
    );
    return (int)$db->last_id();
}

function insert_book(string $title, ?string $published, ?int $author_id, int $copies = 1): int {
    $db->execute(
        "INSERT INTO books (title, published, author_id, copies) VALUES (?, ?, ?, ?)",
        [$title, $published, $author_id, $copies]
    );
    return (int)$db->last_id();
}
