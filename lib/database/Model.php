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

    public static function all()
    {
        return new static();
    }

    public function where(string $col, mixed ...$args): static
    {
        $this->queryBuilder->addSelection($col, ...$args);

        return $this;
    }

    public function first()
    {
        return $this->get(1);
    }

    public static function find(int $id)
    {
        return (new static())->where('id', '=', $id)->first();
    }

    public static function create(array $data)
    {
        if (in_array((new static)->primaryKey, array_keys($data))) {
            unset($data[(new static)->primaryKey]);
        }

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
        if (in_array('created_at', (new static)->fillable)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        if (in_array('updated_at', (new static)->fillable)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $query = "INSERT INTO " . (new static)->table . " (";
        $query .= implode(', ', (new static)->fillable) . ") VALUES ";
        $query .= "(" . implode(', ', array_fill(0, count((new static)->fillable), '?')) . ");";

        $params[0] = "";

        foreach ($data as $key => $value) {
            $params[0] .= gettype($value)[0];
            $params[] = $value;
        }

        (new static)->executeStatement($query, $params);
        return;
    }

    public static function update(array $data)
    {
        $primaryKey = (new static)->primaryKey;

        if (!array_key_exists($primaryKey, $data)) {
            return;
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

        $query = "UPDATE " . (new static)->table . " SET ";

        foreach ($data as $key => $value) {
            $query .= "$key = ?, ";
        }

        $query = rtrim($query, ', ');
        $query .= " WHERE $primaryKey = ?;";

        $params = array_values($data);
        $params[] = $id;

        // (new static)->executeStatement($query, $params);
    }

    public function delete()
    {
        foreach ($this->get() as $row) {
            $query = "DELETE FROM $this->table WHERE $this->primaryKey = ?;";
            $this->executeStatement($query, [$row[$this->primaryKey]]);
        }
    }
}
