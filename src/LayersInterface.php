<?php

namespace Falgun\Midlayer;

use Closure;
use Falgun\Http\RequestInterface;

interface LayersInterface
{

    /**
     * 
     * @param array<int, class-string<MiddlewareInterface>> $layers
     * @param Closure(): mixed $target
     * @param Closure(class-string<MiddlewareInterface>): MiddlewareInterface $resolver
     */
    public function __construct(array $layers, Closure $target, Closure $resolver);

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function next(RequestInterface $request);
}
