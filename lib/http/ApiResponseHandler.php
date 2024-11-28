<?php

namespace lib\http;

class ApiResponseHandler
{
    /**
     * @param mixed $data
     * @param string $message
     * @return void
     */
    public static function checkout(mixed $data, string $message): void
    {
        $response = new Response(ApiResponseStatus::success, $message, $data);

        http_response_code(200);
        $response->throw();
    }

    /**
     * @param int $code
     * @param string $message
     * @param mixed $detail
     * @return void
     */
    public static function throwError(int $code, string $message, mixed $detail = []): void
    {
        $error = new ResponseError($code, ApiErrorCode::getMessage($code), $detail);
        $response = new Response(ApiResponseStatus::error, $message, null, $error);

        http_response_code(200);
        $response->throw();
    }

    /**
     * @param array $additionalData
     * @return void
     */
    public static function debug(array $additionalData = []): void
    {
        $requestInfo = [
            'Method' => $_SERVER['REQUEST_METHOD'],
            'URI' => $_SERVER['REQUEST_URI'],
            'Headers' => getallheaders(),
            'Query Parameters' => $_GET,
            'Post Data' => $_POST,
            'Additional Data' => $additionalData,
        ];

        http_response_code(200);
        header('Content-Type: application/json');

        echo json_encode($requestInfo);
        exit();
    }
}
