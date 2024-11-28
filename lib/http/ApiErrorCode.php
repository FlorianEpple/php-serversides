<?php

namespace lib\http;

class ApiErrorCode
{
    const INVALID_INPUT = 1001;
    const MISSING_PARAMETER = 1002;
    const INVALID_PARAMETER = 1003;
    const INVALID_DATABASE = 2001;
    const INVALID_VAL_TOKEN = 3001;
    const VAL_TOKEN_EXPIRED = 3002;

    /**
     * @var string[] $errorMessages
     */
    private static array $errorMessages = [
        self::INVALID_INPUT => 'Invalid input',
        self::MISSING_PARAMETER => 'Missing parameter',
        self::INVALID_PARAMETER => 'Invalid parameter',
        self::INVALID_DATABASE => 'Invalid database',
        self::INVALID_VAL_TOKEN => 'Invalid validation token',
        self::VAL_TOKEN_EXPIRED => 'Validation token expired',
    ];

    /**
     * @param int $errorCode
     * @return string|null
     */
    public static function getMessage(int $errorCode): ?string
    {
        return self::$errorMessages[$errorCode] ?? null;
    }
}
