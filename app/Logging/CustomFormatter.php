<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class CustomFormatter extends LineFormatter
{
    public function format(LogRecord $record): string
    {
        $datetime = $record->datetime->format('Y-m-d H:i:s');
        $level = str_pad($record->level->name, 8);
        $channel = str_pad($record->channel, 10);

        // Format message
        $message = $record->message;

        // Format context untuk error logs
        $context = '';
        if (! empty($record->context)) {
            $context = $this->formatContext($record->context);
        }

        return sprintf(
            "[%s] %s %s: %s%s\n",
            $datetime,
            $level,
            $channel,
            $message,
            $context
        );
    }

    private function formatContext(array $context): string
    {
        if (empty($context)) {
            return '';
        }

        $formatted = "\n".str_repeat('=', 80)."\n";

        foreach ($context as $key => $value) {
            switch ($key) {
                case 'exception':
                    $formatted .= "Exception: {$value}\n";
                    break;
                case 'message':
                    $formatted .= "Message: {$value}\n";
                    break;
                case 'file':
                    $formatted .= "File: {$value}\n";
                    break;
                case 'line':
                    $formatted .= "Line: {$value}\n";
                    break;
                case 'userId':
                    $formatted .= "User ID: {$value}\n";
                    break;
                case 'trace':
                    if ($value) {
                        $formatted .= "Stack Trace:\n{$value}\n";
                    }
                    break;
                default:
                    if (is_array($value) || is_object($value)) {
                        $formatted .= ucfirst($key).': '.json_encode($value, JSON_PRETTY_PRINT)."\n";
                    } else {
                        $formatted .= ucfirst($key).": {$value}\n";
                    }
            }
        }

        $formatted .= str_repeat('=', 80);

        return $formatted;
    }
}
