<?php

function write_log(string $level, string $message, array $context = []): void
{
    $dir = __DIR__ . '/../logs';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $line = sprintf(
        "[%s] %s: %s %s%s",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message,
        $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '',
        PHP_EOL
    );

    file_put_contents($dir . '/app.log', $line, FILE_APPEND);
}

function register_error_handlers(): void
{
    set_error_handler(function ($severity, $message, $file, $line) {
        write_log('error', $message, ['file' => $file, 'line' => $line, 'severity' => $severity]);
        return false;
    });

    set_exception_handler(function ($exception) {
        write_log('exception', $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        http_response_code(500);
        include __DIR__ . '/../templates/error-500.php';
        exit;
    });
}
