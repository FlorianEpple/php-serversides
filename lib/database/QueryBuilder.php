<?php

namespace lib\database;

class QueryBuilder
{
    private array $query;
    private array $params;

    public function __construct()
    {
        $this->query = [];
        $this->params = [""];
    }

    // Add a selection to the query
    public function addSelection(string $col, mixed ...$args): void
    {
        $value = "";

        // Determine the number of arguments passed
        if (count($args) === 0) {
            // If no arguments are passed, return
            return;
        } else if (count($args) === 1) {
            // If only one argument is passed, assume it is the value
            $value = $args[0];
            $this->query[] = "$col = ?";
        } else {
            // If two arguments are passed, assume the first is the operator and the second is the value
            $value = $args[1];
            $this->query[] = "$col $args[0] ?";
        }

        // Add the value to the params array
        $this->params[] = $value;

        // Determine the type of the value and add it to the params string
        if (gettype($value) === "string") {
            $this->params[0] .= "s";
        } else if (gettype($value) === "integer") {
            $this->params[0] .= "i";
        } else if (gettype($value) === "double") {
            $this->params[0] .= "d";
        } else if (gettype($value) === "NULL") {
            $this->params[0] .= "s";
        } else if (gettype($value) === "boolean") {
            $this->params[0] .= "i";
        } else {
            $this->params[0] .= "s";
        }

        return;
    }

    // Get the query string
    public function getQuery(): ?string
    {
        if (count($this->query) === 0) {
            return null;
        }

        return implode(" AND ", $this->query);
    }

    // Get the params array
    public function getParams(): array
    {
        if (count($this->params) === 1) {
            return [];
        }

        return $this->params;
    }
}
