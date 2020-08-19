<?php

namespace Falgun\Midlayer;

use Closure;

interface LayersInterface
{

    public function __construct(array $layers, Closure $target);

    public function next($request);
}
