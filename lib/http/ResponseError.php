<?php

namespace lib\http;

class ResponseError
{
    private int $code;
    private string $message;
    private mixed $details;

    public function __construct(int $code = 500, string $message = null, mixed $details = null)
    {
        $this->code = $code;
        $this->message = $message ?? "Internal Server Error.";
        $this->details = $details ?? [];
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDetails(): mixed
    {
        return $this->details;
    }
}
