<?php
/**
 * Класс для работы с базой данных
 * Singleton-паттерн, PDO, prepared statements
 * Путь: /includes/db.php
 */

declare(strict_types=1);

if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private int $query_count = 0;

    private function __construct() { $this->connect(); }
    private function __clone() {}

    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function connect(): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->logError('Ошибка подключения к БД: ' . $e->getMessage());
            if (APP_ENV === 'production') {
                throw new RuntimeException('Сервис временно недоступен.');
            }
            throw new RuntimeException('Ошибка подключения к БД: ' . $e->getMessage());
        }
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->prepare($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : null;
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->prepare($sql, $params);
        return $stmt->rowCount();
    }

    public function insert(string $sql, array $params = []): string
    {
        $this->prepare($sql, $params);
        return $this->pdo->lastInsertId();
    }

    private function prepare(string $sql, array $params = []): PDOStatement
    {
        $this->query_count++;
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $param_key = is_int($key) ? $key + 1 : ':' . ltrim((string)$key, ':');
            $param_type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
            $stmt->bindValue($param_key, $value, $param_type);
        }
        $stmt->execute();
        return $stmt;
    }

    public function beginTransaction(): bool { return $this->pdo->beginTransaction(); }
    public function commit(): bool { return $this->pdo->commit(); }
    public function rollback(): bool { return $this->pdo->rollBack(); }

    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        $allowed_tables = ['orders', 'tracks', 'reviews', 'admins', 'contact_messages'];
        if (!in_array($table, $allowed_tables, true)) {
            throw new InvalidArgumentException("Недопустимое имя таблицы: $table");
        }
        return (int)$this->fetchColumn("SELECT COUNT(*) FROM `{$table}` WHERE {$where}", $params);
    }

    public function getQueryCount(): int { return $this->query_count; }

    private function logError(string $message): void
    {
        $log_file = APP_ROOT . '/logs/errors.log';
        $log_dir = dirname($log_file);
        if (!is_dir($log_dir)) mkdir($log_dir, 0750, true);
        error_log("[DB] " . date('Y-m-d H:i:s') . " {$message}\n", 3, $log_file);
    }
}

function db(): Database { return Database::getInstance(); }