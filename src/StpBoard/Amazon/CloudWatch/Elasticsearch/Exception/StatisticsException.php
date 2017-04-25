<?php

namespace StpBoard\Amazon\CloudWatch\Elasticsearch\Exception;

use Exception;
use RuntimeException;

class StatisticsException extends RuntimeException
{
    const ERROR_MESSAGE_PATTERN = 'There was a problem: "%s"';

    const NOT_SUPPORTED_MESSAGE_PATTERN = 'Can not support: "%s"';

    public static function fromPrevious(Exception $exception): self
    {
        return new self(
            sprintf(self::ERROR_MESSAGE_PATTERN, $exception->getMessage()),
            $exception->getCode(),
            $exception
        );
    }

    public static function createForNotSupported(string $message): self
    {
        return new self(
            sprintf(self::NOT_SUPPORTED_MESSAGE_PATTERN, $message)
        );
    }
}
