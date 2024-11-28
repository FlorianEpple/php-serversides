<?php

namespace lib\artisan\commands;

use lib\artisan\Artisan;

define("__TEMPLATES__", __DIR__ . "/../../templates");

class MakeCmds
{
    public static function register()
    {
        // Register controller command
        Artisan::command("controller", [self::class, "makeController"]);
        // Register resource command
        Artisan::command("resource", [self::class, "makeResource"]);
        // Register model command
        Artisan::command("model", [self::class, "makeModel"]);
        // Register table command
        Artisan::command("table", [self::class, "makeTable"]);
    }

    private static function cpyTemplate(string $from, string $to, array $replacements = [])
    {
        $content = file_get_contents(__TEMPLATES__ . "/$from");
        $directory = implode("/", array_slice(explode('/', $to), 0, -1));

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (file_exists($to)) {
            Artisan::error("File already exists: $to");
            return false;
        }

        file_put_contents($to, $content);
        return true;
    }

    public static function makeController(array $args)
    {
        if (count($args) < 2) {
            Artisan::error("Please provide a name for the controller");
            return;
        }

        $targetName = explode('.', $args[1]);
        $name = ucfirst(end($targetName));

        if (!str_ends_with($name, "Controller")) {
            $name .= "Controller";
        }

        $namespace_parts = array_slice($targetName, 0, -1);
        $namespace = implode("\\", $namespace_parts);

        $namespace = count($namespace_parts) > 0 ? "\\" . $namespace : "";

        $dir = __DIR__ . "/../../../app/http/controller";

        $exc = self::cpyTemplate('controller.template', $dir . '/' . implode('/', $namespace_parts) . '/' . $name . '.php', [
            "controller" => $name,
            "namespace" => $namespace
        ]);

        if (!$exc) {
            return;
        }

        Artisan::success("Controller created successfully.");
    }

    public static function makeResource(array $args)
    {
        if (count($args) < 2) {
            Artisan::error("Please provide a name for the resource");
            return;
        }

        $targetName = explode('.', $args[1]);
        $name = ucfirst($targetName[count($targetName) - 1]);

        if (!str_ends_with($name, "Controller")) {
            $name .= "Controller";
        }

        $namespace_parts = array_slice($targetName, 0, -1);
        $namespace = implode("\\", $namespace_parts);

        if (count($namespace_parts) > 0) {
            $namespace = "\\" . $namespace;
        }

        $dir = __DIR__ . "/../../../app/http/controller";

        $exc = self::cpyTemplate('resource.template', $dir . '/' . implode('/', $namespace_parts) . '/' . $name . '.php', [
            "controller" => $name,
            "namespace" => $namespace
        ]);

        if (!$exc) {
            return;
        }

        Artisan::success("Resource created successfully.");
    }

    public static function makeModel(array $args)
    {
        if (count($args) < 2) {
            Artisan::error("Please provide a name for the model");
            return;
        }

        $targetName = explode('.', $args[1]);
        $name = ucfirst($targetName[count($targetName) - 1]);
        $tableName = strtolower($name) . "s";

        $namespace_parts = array_slice($targetName, 0, -1);
        $namespace = implode("\\", $namespace_parts);

        if (count($namespace_parts) > 0) {
            $namespace = "\\" . $namespace;
        }

        $dir = __DIR__ . "/../../../app/models";

        $exc = self::cpyTemplate('model.template', $dir . '/' . implode('/', $namespace_parts)  . $name . '.php', [
            "model" => $name,
            "table" => $tableName,
            "namespace" => $namespace
        ]);

        if (!$exc) {
            return;
        }

        Artisan::success("Model created successfully.");
    }

    public static function makeTable(array $args)
    {
        if (count($args) < 2) {
            Artisan::error("Please provide a name for the table");
            return;
        }

        $targetName = explode('.', $args[1]);
        $name = ucfirst($targetName[count($targetName) - 1]);

        if (str_ends_with($name, "Table")) {
            $name = substr($name, 0, strlen("Table") * -1);
        }

        $table = $name . "Table";
        $tableName = strtolower($name) . "s";

        $namespace_parts = array_slice($targetName, 0, -1);
        $namespace = implode("\\", $namespace_parts);

        if (count($namespace_parts) > 0) {
            $namespace = "\\" . $namespace;
        }

        $dir = __DIR__ . "/../../../app/tables";

        $exc = self::cpyTemplate('table.template', $dir . '/' . implode('/', $namespace_parts)  . $table . '.php', [
            "table" => $table,
            "table_name" => $tableName,
            "model" => $name,
            "namespace" => $namespace
        ]);

        if (!$exc) {
            return;
        }

        Artisan::success("Table created successfully.");
    }
}
