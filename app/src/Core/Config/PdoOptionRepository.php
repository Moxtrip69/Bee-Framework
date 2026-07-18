<?php

declare(strict_types=1);

namespace Bee\Core\Config;

use PDO;

final class PdoOptionRepository implements OptionRepository
{
    private ?PDO $connection = null;

    /** @var array<string, mixed> */
    private array $cache = [];

    /** @var array<string, true> */
    private array $loaded = [];

    /** @var array<string, true> */
    private array $found = [];

    public function __construct(private readonly DatabaseConfig $database)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->loaded[$key])) {
            return isset($this->found[$key]) ? $this->cache[$key] : $default;
        }

        $statement = $this->connection()->prepare(
            'SELECT val FROM options WHERE `option` = :option LIMIT 1'
        );
        $statement->execute(['option' => $key]);
        $value = $statement->fetchColumn();

        $this->loaded[$key] = true;
        if ($value === false) {
            return $default;
        }

        $this->found[$key] = true;
        $this->cache[$key] = $value;

        return $value;
    }

    private function connection(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=%s',
            $this->database->engine,
            $this->database->host,
            $this->database->name,
            $this->database->charset
        );
        $this->connection = new PDO($dsn, $this->database->username, $this->database->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $this->connection;
    }
}
