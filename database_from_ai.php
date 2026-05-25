<?php
/**
 * database.php  –  unified PDO layer for SQLite (dev) and MySQL (production/teacher)
 *
 * Usage:
 *   require_once 'database.php';
 *   $db = Database::get();          // returns the shared PDO instance
 *   $db->insert_user(...);          // call helper methods on it
 *
 * Switch between drivers by changing USE_SQLITE below (or set an env var).
 */

// ─── Configuration ────────────────────────────────────────────────────────────

define('USE_SQLITE', true);          // false → MySQL (XAMPP / teacher's machine)

// SQLite
define('SQLITE_PATH', __DIR__ . '/library.db');

// MySQL  (edit as needed)
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');
define('MYSQL_DB',   'library');

// ─── Database class ───────────────────────────────────────────────────────────

class Database {

    private static ?PDO $instance = null;

    /** Returns the shared PDO connection (singleton). */
    public static function get(): self {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }
        // Return a thin wrapper so we can attach helper methods
        return new self();
    }

    // ── Connection ─────────────────────────────────────────────────────────────

    private static function connect(): PDO {
        if (USE_SQLITE) {
            $pdo = new PDO('sqlite:' . SQLITE_PATH);
            // Enforce foreign keys in SQLite (off by default)
            $pdo->exec('PRAGMA foreign_keys = ON;');
        } else {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                MYSQL_HOST, MYSQL_DB
            );
            $pdo = new PDO($dsn, MYSQL_USER, MYSQL_PASS);
        }

        // Throw exceptions on error instead of silent failures
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Return associative arrays by default
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }

    // ── Raw query helpers ──────────────────────────────────────────────────────

    /**
     * Run a SELECT and return all rows.
     * Example: $db->query("SELECT * FROM books WHERE author_id = ?", [$id])
     */
    public function query(string $sql, array $params = []): array {
        $stmt = self::$instance->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Run a SELECT and return a single row (or null).
     */
    public function query_one(string $sql, array $params = []): ?array {
        $stmt = self::$instance->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Run an INSERT / UPDATE / DELETE and return the number of affected rows.
     */
    public function execute(string $sql, array $params = []): int {
        $stmt = self::$instance->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Returns the last auto-inserted ID. */
    public function last_id(): string {
        return self::$instance->lastInsertId();
    }

    // ── Authors ────────────────────────────────────────────────────────────────

    public function get_authors(): array {
        return $this->query("SELECT * FROM authors ORDER BY name");
    }

    public function get_author(int $id): ?array {
        return $this->query_one("SELECT * FROM authors WHERE id = ?", [$id]);
    }

    /**
     * @param string      $name   Full name
     * @param string|null $born   'YYYY-MM-DD' or null
     * @param string|null $died   'YYYY-MM-DD' or null
     * @return int  New author ID
     */
    public function insert_author(string $name, ?string $born, ?string $died): int {
        $this->execute(
            "INSERT INTO authors (name, born, died) VALUES (?, ?, ?)",
            [$name, $born, $died]
        );
        return (int) $this->last_id();
    }

    public function update_author(int $id, string $name, ?string $born, ?string $died): void {
        $this->execute(
            "UPDATE authors SET name = ?, born = ?, died = ? WHERE id = ?",
            [$name, $born, $died, $id]
        );
    }

    public function delete_author(int $id): void {
        $this->execute("DELETE FROM authors WHERE id = ?", [$id]);
    }

    // ── Books ──────────────────────────────────────────────────────────────────

    public function get_books(): array {
        return $this->query("
            SELECT b.*, a.name AS author_name
            FROM   books b
            LEFT JOIN authors a ON a.id = b.author_id
            ORDER BY b.title
        ");
    }

    public function get_book(int $id): ?array {
        return $this->query_one("
            SELECT b.*, a.name AS author_name
            FROM   books b
            LEFT JOIN authors a ON a.id = b.author_id
            WHERE  b.id = ?
        ", [$id]);
    }

    /**
     * @return int  New book ID
     */
    public function insert_book(string $title, ?string $published, ?int $author_id, int $copies = 1): int {
        $this->execute(
            "INSERT INTO books (title, published, author_id, copies) VALUES (?, ?, ?, ?)",
            [$title, $published, $author_id, $copies]
        );
        return (int) $this->last_id();
    }

    public function update_book(int $id, string $title, ?string $published, ?int $author_id, int $copies): void {
        $this->execute(
            "UPDATE books SET title = ?, published = ?, author_id = ?, copies = ? WHERE id = ?",
            [$title, $published, $author_id, $copies, $id]
        );
    }

    public function delete_book(int $id): void {
        $this->execute("DELETE FROM books WHERE id = ?", [$id]);
    }

    // ── Users ──────────────────────────────────────────────────────────────────

    public function get_users(): array {
        return $this->query("SELECT * FROM users ORDER BY name");
    }

    public function get_user(int $id): ?array {
        return $this->query_one("SELECT * FROM users WHERE id = ?", [$id]);
    }

    /**
     * @return int  New user ID
     */
    public function insert_user(string $name, ?string $born, ?string $email): int {
        $this->execute(
            "INSERT INTO users (name, born, email) VALUES (?, ?, ?)",
            [$name, $born, $email]
        );
        return (int) $this->last_id();
    }

    public function update_user(int $id, string $name, ?string $born, ?string $email): void {
        $this->execute(
            "UPDATE users SET name = ?, born = ?, email = ? WHERE id = ?",
            [$name, $born, $email, $id]
        );
    }

    public function delete_user(int $id): void {
        $this->execute("DELETE FROM users WHERE id = ?", [$id]);
    }

    // ── Loans ──────────────────────────────────────────────────────────────────

    public function get_loans(?int $user_id = null): array {
        $where  = $user_id !== null ? "WHERE l.user_id = ?" : "";
        $params = $user_id !== null ? [$user_id]              : [];
        return $this->query("
            SELECT l.*,
                   u.name  AS user_name,
                   b.title AS book_title
            FROM   loans l
            JOIN   users u ON u.id = l.user_id
            JOIN   books b ON b.id = l.book_id
            $where
            ORDER BY l.loaned_on DESC
        ", $params);
    }

    public function get_loan(int $id): ?array {
        return $this->query_one("SELECT * FROM loans WHERE id = ?", [$id]);
    }

    /**
     * @return int  New loan ID
     */
    public function insert_loan(int $user_id, int $book_id, ?string $loaned_on, ?string $due_on): int {
        $this->execute(
            "INSERT INTO loans (user_id, book_id, processed_at, loaned_on, due_on)
             VALUES (?, ?, CURRENT_TIMESTAMP, ?, ?)",
            [$user_id, $book_id, $loaned_on, $due_on]
        );
        return (int) $this->last_id();
    }

    /** Mark a loan as returned by setting due_on to today. */
    public function return_loan(int $id): void {
        $this->execute(
            "UPDATE loans SET due_on = CURRENT_DATE WHERE id = ?",
            [$id]
        );
    }

    public function delete_loan(int $id): void {
        $this->execute("DELETE FROM loans WHERE id = ?", [$id]);
    }
}
?>
