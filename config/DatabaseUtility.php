<?php
/**
 * DatabaseUtility.php
 * --------------------------------------------------
 * Author: Gaurav Kumar
 * DB: delphian_logic (MySQL 9.x)
 * --------------------------------------------------
 */

declare(strict_types=1);

class DatabaseUtility
{
    private static ?DatabaseUtility $instance = null;
    private PDO $conn;

    private string $host = 'localhost';
    private string $db   = 'delphian_logic';
    private string $user = 'root';
    private string $pass = '';
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            die('Database connection failed');
        }
    }


    public static function getInstance(): DatabaseUtility
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function getConnection(): PDO
    {
        return $this->conn;
    }

    /**
     * Execute Prepared Query
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch All Rows
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch Single Row
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Insert Record
     */
    public function insert(string $table, array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn ($f) => ':' . $f, $fields);

        $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $this->query($sql, $data);

        return (int) $this->conn->lastInsertId();
    }

    /**
     * Update Record
     */
    public function update(string $table, array $data, string $where, array $whereParams): int
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array_merge($data, $whereParams));

        return $stmt->rowCount();
    }

    /**
     * Delete Record
     */
    public function delete(string $table, string $where, array $params): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Transactions
     */
    public function begin(): void
    {
        $this->conn->beginTransaction();
    }

    public function commit(): void
    {
        $this->conn->commit();
    }

    public function rollback(): void
    {
        $this->conn->rollBack();
    }

    public function getActiveCategories(): array
    {
        return $this->fetchAll(
            "SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order"
        );
    }

      public function getllCategories(): array
    {
        return $this->fetchAll(
            "SELECT * FROM categories ORDER BY display_order"
        );
    }

    /**
     * Get Topics by Category (Active Only)
     */
    public function getTopicsByCategory(int $categoryId): array
    {
        return $this->fetchAll(
            "SELECT * FROM topics WHERE category_id = :cid AND is_active = 1 ORDER BY display_order",
            ['cid' => $categoryId]
        );
    }



    /**
     * Get Setting with Auto Type Casting
     */
public function getSetting(string $key): mixed
{
    $row = $this->fetch(
        "SELECT setting_value, data_type 
         FROM settings 
         WHERE setting_key = :key 
         LIMIT 1",
        ['key' => $key]
    );

    if (!$row) {
        return null;
    }

    return match ($row['data_type']) {
        'boolean' => filter_var($row['setting_value'], FILTER_VALIDATE_BOOLEAN),
        'integer' => (int)$row['setting_value'],
        default   => $row['setting_value'],
    };
}
}