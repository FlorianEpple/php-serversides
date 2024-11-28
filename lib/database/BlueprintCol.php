<?php

namespace lib\database;

class BlueprintCol
{
    private string $name;
    private string $type;

    private bool $nullable = false;
    private mixed $default = null;
    private bool $unique = false;
    private bool $primary = false;
    private bool $autoIncrement = false;
    private ?string $references = null;
    private ?string $fkOnUpdate = null;
    private ?string $fkOnDelete = null;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function build(): array
    {
        $build = [];
        $add = [];

        $str = $this->name;
        $str .= " " . $this->type;

        if (!$this->nullable) {
            $str .= " NOT NULL";
        }

        if ($this->default !== null) {
            $str .= " DEFAULT ";
            $str .= $this->default;
        }

        if ($this->unique) {
            $str .= " UNIQUE";
        }

        if ($this->primary) {
            $add[] = "PRIMARY KEY (" . $this->name . ")";
        }

        if ($this->autoIncrement) {
            $str .= " AUTO_INCREMENT";
        }

        if ($this->references !== null) {
            $tmp = "FOREIGN KEY (";
            $tmp .= $this->name;
            $tmp .= ") REFERENCES ";
            $tmp .= $this->references;

            if ($this->fkOnUpdate !== null) {
                $tmp .= " ON UPDATE " . $this->fkOnUpdate;
            }

            if ($this->fkOnDelete !== null) {
                $tmp .= " ON DELETE " . $this->fkOnDelete;
            }

            $add[] = $tmp;
        }


        $build[] = $str;
        $build[] = $add;

        return $build;
    }

    public function nullable(): BlueprintCol
    {
        $this->nullable = true;
        return $this;
    }

    public function default(mixed $default): BlueprintCol
    {
        $this->default = $default;
        return $this;
    }

    public function unique(): BlueprintCol
    {
        $this->unique = true;
        return $this;
    }

    public function primary(): BlueprintCol
    {
        $this->primary = true;
        return $this;
    }

    public function autoIncrement(): BlueprintCol
    {
        $this->autoIncrement = true;
        return $this;
    }

    public function foreign(string $table, string $col): BlueprintCol
    {
        $this->references = $table . "(" . $col . ")";
        return $this;
    }

    public function onUpdateRestrict(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnUpdate = "RESTRICT";
        }

        return $this;
    }

    public function onUpdateCascade(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnUpdate = "CASCADE";
        }

        return $this;
    }

    public function onUpdateSetNull(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnUpdate = "SET NULL";
        }

        return $this;
    }

    public function onUpdateNoAction(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnUpdate = "NO ACTION";
        }

        return $this;
    }

    public function onUpdateSetDefault(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnUpdate = "SET DEFAULT";
        }

        return $this;
    }

    public function onDeleteRestrict(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnDelete = "RESTRICT";
        }

        return $this;
    }

    public function onDeleteCascade(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnDelete = "CASCADE";
        }

        return $this;
    }

    public function onDeleteSetNull(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnDelete = "SET NULL";
        }

        return $this;
    }

    public function onDeleteNoAction(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnDelete = "NO ACTION";
        }

        return $this;
    }

    public function onDeleteSetDefault(): BlueprintCol
    {
        if ($this->references !== null) {
            $this->fkOnDelete = "SET DEFAULT";
        }

        return $this;
    }
}
