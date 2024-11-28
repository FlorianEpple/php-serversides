<?php

namespace lib\database;

use Exception;
use lib\Env;
use lib\http\ApiErrorCode;
use lib\http\ApiResponseHandler;
use mysqli;

class MysqlConnection
{
    protected ?mysqli $connection = null;

    public function __construct()
    {
        try {
            $this->connection = new mysqli(Env::get("MYSQL_HOST", "localhost"), Env::get("MYSQL_USERNAME", "root"), Env::get("MYSQL_PASSWORD", ""), Env::get("MYSQL_DATABASE", ""));

            if (mysqli_connect_errno()) {
                ApiResponseHandler::throwError(ApiErrorCode::INVALID_DATABASE, "Could not connect to lib for user " . Env::get("MYSQL_USERNAME", "root") . ".");
                // echo "Could not connect to lib for user " . env("MYSQL_USERNAME", "") . ".";
                // exit();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function select($query = "", $params = [])
    {
        try {
            if (!$this->connection) {
                return;
            }

            $stmt = $this->executeStatement($query, $params);

            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
            // ApiResponseHandler::throwError(ApiErrorCode::INVALID_DATABASE, $e->getMessage());
        }
    }

    public function executeStatement($query = "", $params = [])
    {
        try {
            if (!$this->connection) {
                return;
            }

            $stmt = $this->connection->prepare($query);

            if ($stmt === false) {
                echo "Unable to do prepared statement: " . $query;
                exit();
                // ApiResponseHandler::throwError(ApiErrorCode::INVALID_DATABASE, "Unable to do prepared statement: " . $query);
            }

            if ($params) {
                $paramTypes = array_shift($params);
                $stmt->bind_param($paramTypes, ...$params);
            }

            $stmt->execute();

            return $stmt;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
            // ApiResponseHandler::throwError(ApiErrorCode::INVALID_DATABASE, $e->getMessage());
        }
    }
}
