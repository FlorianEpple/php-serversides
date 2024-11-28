<?php

namespace lib\database;

abstract class Model extends MysqlConnection
{
    public string $table;
    public string $primaryKey;

    public array $fillable;
    public array $restricted;

    private QueryBuilder $queryBuilder;

    public function __construct()
    {
        parent::__construct();

        $this->table = $this->table ?? strtolower((new \ReflectionClass($this))->getShortName()) . 's';
        $this->primaryKey = $this->primaryKey ?? 'id';

        $this->fillable = $this->fillable ?? [];
        $this->restricted = $this->restricted ?? ['password'];

        $this->queryBuilder = new QueryBuilder();
    }

    public function get(int $limit = 100): array
    {
        $query = "SELECT * FROM $this->table";

        if ($this->queryBuilder->getQuery()) {
            $query .= " WHERE " . $this->queryBuilder->getQuery();
        }

        $query .= " LIMIT $limit;";

        $result = $this->select($query, $this->queryBuilder->getParams());

        foreach ($result as &$row) {
            foreach ($row as $key => $col) {
                if (in_array($key, $this->restricted)) {
                    unset($row[$key]);
                }
            }
        }

        return $result;
    }

    public static function all(): static
    {
        return new static();
    }

    public function where(string $col, mixed ...$args): static
    {
        $this->queryBuilder->addSelection($col, ...$args);

        return $this;
    }

    public function first(): array
    {
        return $this->get(1);
    }

    public static function find(int $id): array
    {
        return (new static())->where('id', '=', $id)->first();
    }

    public static function findOrFail(int $id): array
    {
        $result = (new static())->where('id', '=', $id)->first();

        if (!$result) {
            throw new \Error("No record found");
        }

        return $result;
    }

    public static function getNextId(): int
    {
        $query = "SELECT MAX(id) as id FROM " . (new static)->table . ";";
        $result = (new static)->select($query);

        return $result[0]['id'] + 1;
    }

    public static function create(array $data): array
    {
        $data = array_filter($data, function ($key) {
            return in_array($key, (new static)->fillable);
        }, ARRAY_FILTER_USE_KEY);

        $data = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);

        $data = array_map(function ($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $data);

        // add next id to the data
        $data['id'] = (new static)->getNextId();

        // check if table has timestamps from the sql table
        if (in_array('created_at', (new static)->fillable)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        if (in_array('updated_at', (new static)->fillable)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $params[0] = "";
        $query = "INSERT INTO " . (new static)->table . " (";
        $query .= implode(', ', array_keys($data)) . ") VALUES ";

        foreach ($data as $value) {
            $params[0] .= gettype($value)[0];
            $params[] = $value;
        }

        $query .= "(" . implode(', ', array_fill(0, count((new static)->fillable), '?')) . ");";

        (new static)->executeStatement($query, $params);

        return (new static)->find($data['id']);
    }

    public static function update(array $data): array
    {
        $primaryKey = (new static)->primaryKey;

        if (!array_key_exists($primaryKey, $data)) {
            throw new \Error("Primary key not found in data");
        }

        $id = $data[$primaryKey];

        $data = array_filter($data, function ($key) {
            return in_array($key, (new static)->fillable);
        }, ARRAY_FILTER_USE_KEY);

        $data = array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);

        $data = array_map(function ($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $data);

        // check if table has timestamps from the sql table

        if (in_array('updated_at', (new static)->fillable)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $params[0] = "";

        $query = "UPDATE " . (new static)->table . " SET ";

        foreach ($data as $key => $value) {
            if ($key === $primaryKey) {
                continue;
            }

            $params[0] .= gettype($value)[0];
            $params[] = $value;
            $query .= "$key = ?, ";
        }

        $query = rtrim($query, ', ');
        $query .= " WHERE $primaryKey = ?;";

        $params[0] .= gettype($id)[0];
        $params[] = $id;

        (new static)->executeStatement($query, $params);

        return (new static)->find($id);
    }

    public static function delete(mixed $id): void
    {
        $query = "DELETE FROM " . (new static)->table . " WHERE id = ?;";

        $type = strtolower(gettype($id)[0]);

        (new static)->executeStatement($query, [$type, $id]);
    }
}
