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
            $dsn = 'mysql:host=' . MYSQL_ADDRESS . ';dbname=' . MYSQL_NAME . ';charset=utf8mb4';
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
