<?php
declare(strict_types=1);

namespace Falgun\Midlayer\Tests;

use Falgun\Http\Request;

final class RequestBuilder
{

    public static function build(): Request
    {
        $_SERVER = [
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '8080',
            'SERVER_ADDR' => '127.0.0.1',
            'HTTP_HOST' => 'localhost:8080',
            'REQUEST_URI' => '/falgun-skeleton/public/?test=true',
            'REQUEST_METHOD' => 'post',
            'QUERY_STRING' => 'test=true',
            'REQUEST_SCHEME' => 'http',
            'SCRIPT_FILENAME' => '/home/user/falgun-skeleton/public/index.php',
            'SCRIPT_NAME' => '/falgun-skeleton/public/index.php',
            'PHP_SELF' => '/falgun-skeleton/public/index.php',
        ];
        $_GET = ['test' => 'true'];
        $_POST = ['post' => 'true'];
        $_COOKIE = [];
        $_FILES = [];


        return Request::createFromGlobals();
    }
}
