<?php

namespace Falgun\Midlayer;

interface MidlayerInterface
{

    public function __construct(array $layers = [], string $layerStackClass = Layers::class);
}
