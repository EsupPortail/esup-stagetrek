<?php

namespace API\Service\Interfaces;

interface ApiRestServiceInterface
{
    /**
     * Get params API key
     *
     * @return string
     */
    public static function getParamsApiKey(): string;
}