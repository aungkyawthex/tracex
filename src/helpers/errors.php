<?php

function isDebugMode(): bool
{
    return filter_var((string) getenv('TRACEX_DEBUG'), FILTER_VALIDATE_BOOLEAN);
}

function logThrowable(Throwable $e, string $context = ''): void
{
    error_log(sprintf(
        'tracex: %s%s: %s in %s:%d',
        $context !== '' ? $context . ' - ' : '',
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));
}

function abortWithError(string $message, ?Throwable $e = null): void
{
    if ($e !== null) {
        logThrowable($e, 'aborted request');
    }

    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }

    // Internal details are only for local debugging; users see a generic message.
    $detail = $e !== null && isDebugMode() ? $e->getMessage() : '';

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Error - TraceX</title></head>'
        . '<body style="font-family:sans-serif;padding:2rem">'
        . '<h1 style="font-size:1.25rem">' . htmlspecialchars($message) . '</h1>'
        . ($detail !== '' ? '<pre>' . htmlspecialchars($detail) . '</pre>' : '')
        . '</body></html>';

    exit(1);
}

function registerErrorHandlers(): void
{
    static $registered = false;
    if ($registered) {
        return;
    }
    $registered = true;

    error_reporting(E_ALL);
    ini_set('log_errors', '1');
    ini_set('display_errors', isDebugMode() ? '1' : '0');

    set_exception_handler(function (Throwable $e): void {
        abortWithError('Something went wrong. Please try again later.', $e);
    });

    register_shutdown_function(function (): void {
        $error = error_get_last();
        $fatal = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;

        if ($error !== null && ($error['type'] & $fatal) !== 0) {
            abortWithError('Something went wrong. Please try again later.');
        }
    });
}
