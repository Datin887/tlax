<?php
/**
 * Класс для работы с базой данных
 * Singleton-паттерн, PDO, prepared statements
 * 
 * Путь: /includes/db.php
 */

declare(strict_types=1);

// Защита от прямого доступа
if (!defined('APP_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

class Database
{
    // Единственный экземпляр класса (Singleton)
    private static ?Database $instance = null;

    // PDO-соединение
    private PDO $pdo;

    // Счётчик запросов (для отладки)
    private int $query_count = 0;

    /**
     * Приватный конструктор — запрещает создание через new Database()
     * Используй Database::getInstance()
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Клонирование запрещено (паттерн Singleton)
     */
    private function __clone() {}

    /**
     * Получение единственного экземпляра базы данных
     */
    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Создание PDO-соединения с базой данных
     * @throws RuntimeException при ошибке подключения
     */
    private function connect(): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            // Выбрасывать исключения при ошибках
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Возвращать результат как ассоциативный массив
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Отключаем эмуляцию prepared statements (безопаснее)
            PDO::ATTR_EMULATE_PREPARES   => false,

            // Получать числовые поля как числа, а не строки
            PDO::ATTR_STRINGIFY_FETCHES  => false,

            // Кодировка соединения
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",

            // Persistent connections (осторожно в production!)
            PDO::ATTR_PERSISTENT         => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Логируем ошибку (без чувствительных данных)
            $this->logError('Ошибка подключения к БД: ' . $e->getMessage());

            // В production — показываем общее сообщение
            if (APP_ENV === 'production') {
                throw new RuntimeException('Сервис временно недоступен. Попробуйте позже.');
            }
            throw new RuntimeException('Ошибка подключения к БД: ' . $e->getMessage());
        }
    }

    /**
     * Выполнение SELECT-запроса, возвращает все строки
     * 
     * @param string $sql   SQL с плейсхолдерами (:name или ?)
     * @param array  $params Параметры для подстановки
     * @return array Массив строк результата
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->prepare($sql, $params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError('fetchAll error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при получении данных: ' . $e->getMessage());
        }
    }

    /**
     * Выполнение SELECT-запроса, возвращает одну строку
     * 
     * @param string $sql
     * @param array  $params
     * @return array|null Одна строка или null
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $result = $stmt->fetch();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            $this->logError('fetchOne error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при получении записи: ' . $e->getMessage());
        }
    }

    /**
     * Получение одного значения из запроса (первая колонка первой строки)
     * Удобно для COUNT(*), MAX(), и т.д.
     * 
     * @param string $sql
     * @param array  $params
     * @return mixed Значение или null
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        try {
            $stmt = $this->prepare($sql, $params);
            $result = $stmt->fetchColumn();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            $this->logError('fetchColumn error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при получении значения: ' . $e->getMessage());
        }
    }

    /**
     * Выполнение INSERT / UPDATE / DELETE запроса
     * 
     * @param string $sql
     * @param array  $params
     * @return int Количество затронутых строк
     */
    public function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = $this->prepare($sql, $params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError('execute error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при выполнении запроса: ' . $e->getMessage());
        }
    }

    /**
     * Вставка записи с возвратом ID
     * 
     * @param string $sql
     * @param array  $params
     * @return string ID последней вставленной записи
     */
    public function insert(string $sql, array $params = []): string
    {
        try {
            $this->prepare($sql, $params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->logError('insert error: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new RuntimeException('Ошибка при добавлении записи: ' . $e->getMessage());
        }
    }

    /**
     * Подготовка и выполнение запроса
     * Внутренний метод — используется всеми публичными методами
     * 
     * @param string $sql
     * @param array  $params
     * @return PDOStatement
     */
    private function prepare(string $sql, array $params = []): PDOStatement
    {
        $this->query_count++;

        $stmt = $this->pdo->prepare($sql);

        // Привязываем параметры с правильными типами PDO
        foreach ($params as $key => $value) {
            $param_key = is_int($key) ? $key + 1 : ':' . ltrim((string)$key, ':');
            $param_type = match (true) {
                is_int($value)   => PDO::PARAM_INT,
                is_bool($value)  => PDO::PARAM_BOOL,
                is_null($value)  => PDO::PARAM_NULL,
                default          => PDO::PARAM_STR,
            };
            $stmt->bindValue($param_key, $value, $param_type);
        }

        $stmt->execute();
        return $stmt;
    }

    // ======================================================
    // ТРАНЗАКЦИИ
    // ======================================================

    /**
     * Начать транзакцию
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Подтвердить транзакцию
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Откатить транзакцию
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Выполнить callable внутри транзакции с автоматическим rollback при ошибке
     * 
     * Пример использования:
     * $db->transaction(function() use ($db) {
     *     $db->execute("INSERT ...");
     *     $db->execute("UPDATE ...");
     * });
     * 
     * @param callable $callback
     * @return mixed Результат callback
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            $this->logError('Транзакция откатилась: ' . $e->getMessage());
            throw $e;
        }
    }

    // ======================================================
    // ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ
    // ======================================================

    /**
     * Получить количество строк в таблице с условием
     * 
     * @param string $table  Имя таблицы
     * @param string $where  WHERE условие (без слова WHERE)
     * @param array  $params Параметры
     * @return int
     */
    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        // Whitelist для имён таблиц (защита от SQL-инъекций в имени таблицы)
        $allowed_tables = [
            'orders', 'tracks', 'reviews', 'admins', 'admin_sessions',
            'order_logs', 'track_plays', 'page_views', 'contact_messages',
        ];

        if (!in_array($table, $allowed_tables, true)) {
            throw new InvalidArgumentException("Недопустимое имя таблицы: $table");
        }

        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$where}";
        return (int)$this->fetchColumn($sql, $params);
    }

    /**
     * Проверить, существует ли запись
     * 
     * @param string $table
     * @param string $where
     * @param array  $params
     * @return bool
     */
    public function exists(string $table, string $where, array $params = []): bool
    {
        return $this->count($table, $where, $params) > 0;
    }

    /**
     * Получить количество выполненных запросов (для отладки)
     */
    public function getQueryCount(): int
    {
        return $this->query_count;
    }

    /**
     * Получить прямой доступ к PDO (для нестандартных случаев)
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Логирование ошибок в файл
     * 
     * @param string $message
     */
    private function logError(string $message): void
    {
        $log_file = APP_ROOT . '/logs/errors.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $uri = $_SERVER['REQUEST_URI'] ?? 'CLI';

        $log_line = "[{$timestamp}] [DB] [{$ip}] [{$uri}] {$message}" . PHP_EOL;

        // Создаём директорию логов если не существует
        $log_dir = dirname($log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0750, true);
        }

        error_log($log_line, 3, $log_file);
    }
}

/**
 * Глобальная функция-хелпер для получения экземпляра БД
 * Удобно использовать вместо Database::getInstance() в шаблонах
 * 
 * Пример: $db = db();
 *         $tracks = db()->fetchAll("SELECT * FROM tracks");
 */
function db(): Database
{
    return Database::getInstance();
}