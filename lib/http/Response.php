<?php

namespace lib\http;

class Response
{
    public ApiResponseStatus $status;
    public string $message;
    public mixed $data;
    public ?ResponseError $error;
    public int $code;

    public function __construct(ApiResponseStatus $status, string $message, mixed $data, ?ResponseError $error = null, int $code = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->error = $error;
        $this->code = $code === null ? ($error ? $error->getCode() : 200) : $code;
    }

    public function throw(): void
    {
        header('Content-Type: application/json');

        $response = [
            'status' => $this->status->name,
            'message' => $this->message,
            'data' => $this->data,
            'error' => $this->error ? [
                'code' => $this->error->getCode(),
                'message' => $this->error->getMessage(),
                'details' => $this->error->getDetails(),
            ] : null,
        ];

        http_response_code($this->code);
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }

    public static function withMessage(string $message, int $code = 200): Response
    {
        return new Response(ApiResponseStatus::success, $message, [], null, $code);
    }

    public static function json(mixed $data, string $message = null, int $code = 200): Response
    {
        return new Response(ApiResponseStatus::success, $message, $data, null, $code);
    }

    public static function withError(string $message, ?ResponseError $error = null): Response
    {
        return new Response(ApiResponseStatus::error, $message, null, $error);
    }
}
