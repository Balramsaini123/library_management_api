<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LoggingService
{
    /**
     * Logs a message to the given channel.
     *
     * @param  string  $channel  The name of the log channel to use.
     * @param  string  $message  The log message.
     * @param  array  $context  The context to add to the log message.
     */
    public function log(string $channel, string $message, array $context = []): void
    {
        Log::channel($channel)->info($message, $context);
    }

    /**
     * Logs an error message to the given channel.
     *
     * @param  string  $channel  The name of the log channel to use.
     * @param  string  $message  The log message.
     * @param  array  $context  The context to add to the log message.
     */
    public function error(string $channel, string $message, array $context = []): void
    {
        Log::channel($channel)->error($message, $context);
    }
}
