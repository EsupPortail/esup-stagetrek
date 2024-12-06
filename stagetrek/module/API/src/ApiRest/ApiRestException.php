<?php

namespace API\ApiRest;


use RuntimeException;

class ApiRestException extends RuntimeException
{
    /**
     * ApiRestException constructor.
     * @param string $statusCode
     * @param string $content
     */
    public function __construct(string $statusCode, string $content)
    {
        parent::__construct(sprintf("[%s] %s", $statusCode, $content));
    }
}