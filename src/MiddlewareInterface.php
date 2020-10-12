<?php

namespace Falgun\Midlayer;

use Falgun\Http\RequestInterface;

interface MiddlewareInterface
{

    /**
     * @param RequestInterface $request
     * @param Layers $layers
     * @return mixed from inner layer (controller)
     */
    public function handle(RequestInterface $request, LayersInterface $layers);
}
