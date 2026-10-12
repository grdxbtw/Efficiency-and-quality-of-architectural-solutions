<?php

// Спільний інтерфейс будівельника запитів
interface DatabaseQueryBuilder
{
    public function select(string $columns): self;
    public function where(string $condition): self;
    public function limit(int $count): self;
    public function getSQL(): string;
}

// Будівельник для PostgreSQL
class PostgresBuilder implements DatabaseQueryBuilder
{
    private array $queryParts = [];

    public function select(string $columns): self
    {
        $this->queryParts['select'] = "SELECT {$columns}";
        return $this;
    }

    public function where(string $condition): self
    {
        $this->queryParts['where'] = "WHERE {$condition}";
        return $this;
    }

    public function limit(int $count): self
    {
        $this->queryParts['limit'] = "LIMIT {$count}";
        return $this;
    }

    public function getSQL(): string
    {
        $sql = implode(" ", $this->queryParts);
        $this->queryParts = [];
        return $sql . " (PostgreSQL Driver)";
    }
}

// Будівельник для MySQL
class MysqlBuilder implements DatabaseQueryBuilder
{
    private array $queryParts = [];

    public function select(string $columns): self
    {
        $this->queryParts['select'] = "SELECT {$columns}";
        return $this;
    }

    public function where(string $condition): self
    {
        $this->queryParts['where'] = "WHERE {$condition}";
        return $this;
    }

    public function limit(int $count): self
    {
        $this->queryParts['limit'] = "LIMIT {$count}";
        return $this;
    }

    public function getSQL(): string
    {
        $sql = implode(" ", $this->queryParts);
        $this->queryParts = [];
        return $sql . " (MySQL Driver)";
    }
}

// Клієнтський код
$pgBuilder = new PostgresBuilder();
$pgQuery = $pgBuilder->select("id, username")
    ->where("status = 'active'")
    ->limit(50)
    ->getSQL();
echo "Сформовано: " . $pgQuery . PHP_EOL;

$mysqlBuilder = new MysqlBuilder();
$myQuery = $mysqlBuilder->select("title, body")
    ->limit(10)
    ->getSQL();
echo "Сформовано: " . $myQuery . PHP_EOL;
