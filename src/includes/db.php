<?php
/**
 * Database Singleton class using PDO
 *
 * Путь: /includes/db.php
 */

declare(strict_types=1);

class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $this->pdo = new \PDO($dsn, DB_USER, DB_PASS, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE=> \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Выполнить запрос (INSERT, UPDATE, DELETE)
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Выполнить INSERT и вернуть lastInsertId
     */
    public function insert(string $sql, array $params = []): int
    {
        $this->execute($sql, $params);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Получить одну строку
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Получить все строки
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Получить количество строк
     */
    public function count(string $sql, array $params = []): int
    {
        $row = $this->fetchOne($sql, $params);
        return (int)reset($row);
    }

    /**
     * Получить нативный PDO для прямого доступа
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}