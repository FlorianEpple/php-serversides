<?php

namespace lib\database;

class Blueprint extends MysqlConnection
{
    private array $cols = [];

    private function addCol(string $name, string $type)
    {
        $col = new BlueprintCol($name, $type);
        $this->cols[] = $col;

        return $col;
    }

    public function build(string $name)
    {
        $adds = [];

        $query = "CREATE TABLE IF NOT EXISTS ";
        $query .= $name;
        $query .= "(";

        for ($i = 0; $i < count($this->cols); $i++) {
            $colBuild = $this->cols[$i]->build();

            if (count($colBuild) > 0) {
                if ($i !== 0) {
                    $query .= ", ";
                }

                $query .= $colBuild[0];
            }

            if (count($colBuild) > 1) {
                $adds = array_merge($adds, $colBuild[1]);
            }
        }

        foreach ($adds as $addon) {
            $query .= ", ";
            $query .= $addon;
        }

        $query .= ");";

        $this->executeStatement($query);
    }

    public function id(string $name = 'id'): void
    {
        $this->addCol($name, "BIGINT")
            ->primary()
            ->autoIncrement();
    }

    public function string(string $name, int $length = 255): BlueprintCol
    {
        return $this->addCol($name, "VARCHAR(" . $length . ")");
    }

    public function integer(string $name): BlueprintCol
    {
        return $this->addCol($name, "INT");
    }

    public function timestamps(): void
    {

        $this->addCol('created_at', "DATETIME")->default('CURRENT_TIMESTAMP');
        $this->addCol('updated_at', "DATETIME")->default('CURRENT_TIMESTAMP');
    }
}
