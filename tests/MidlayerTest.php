<?php
declare(strict_types=1);

namespace Falgun\Midlayer\Tests;

use Falgun\Midlayer\Midlayer;
use PHPUnit\Framework\TestCase;
use Falgun\Midlayer\MiddlewareInterface;

final class MidlayerTest extends TestCase
{

    public function testMidlayerRun()
    {
        $request = RequestBuilder::build();
        $layers = [Stubs\FakeMiddleware::class];

        $midlayer = new Midlayer($layers);

        $midlayer->setResolver(function(string $class): MiddlewareInterface {
            return new $class();
        });

        $response = $midlayer->run($request, function() {
            return true;
        });

        $this->assertSame(true, $response);
    }

    public function testEmptyMidlayer()
    {
        $request = RequestBuilder::build();
        $layers = [];

        $midlayer = new Midlayer($layers);

        $response = $midlayer->run($request, function() {
            return true;
        });

        $this->assertSame(true, $response);
    }

    public function testInvalidMidlayer()
    {
        $request = RequestBuilder::build();
        $layers = [Stubs\NotMiddleware::class];

        $midlayer = new Midlayer($layers);

        $this->expectException(\TypeError::class);

        $midlayer->run($request, function() {
            return true;
        });
    }

    public function testMultiMidlayer()
    {
        $request = RequestBuilder::build();
        $layers = [Stubs\FakeMiddleware::class, Stubs\FakeMiddleware::class];

        $midlayer = new Midlayer($layers);

        $response = $midlayer->run($request, function() {
            return false;
        });

        $this->assertSame(false, $response);
        $this->assertSame(2, $request->attributes()->get('layers'));
    }

    public function testMidlayerAppenPrepend()
    {
        $request = RequestBuilder::build();

        $midlayer = new Midlayer();
        $midlayer->prepend(Stubs\FakeMiddleware::class);
        $midlayer->prepend(Stubs\FakeMiddleware::class);
        $midlayer->append(Stubs\AnotherFakeMiddleware::class);
        $midlayer->prepend(Stubs\FakeMiddleware::class);
        $midlayer->prepend(Stubs\AnotherFakeMiddleware::class);

        $response = $midlayer->run($request, function() {
            return false;
        });

        $this->assertSame(false, $response);
        $this->assertSame(5, $request->attributes()->get('layers'));
        $this->assertSame(
            ['AnotherFakeMiddleware', 'FakeMiddleware', 'FakeMiddleware', 'FakeMiddleware', 'AnotherFakeMiddleware'],
            $request->attributes()->get('middlewares'));
    }
}
