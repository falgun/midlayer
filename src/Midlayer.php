<?php
declare(strict_types=1);

namespace Falgun\Midlayer;

use Closure;

class Midlayer implements MidlayerInterface
{

    protected array $layers;
    protected string $layerStackClass;
    protected $resolver;

    public function __construct(array $layers = [], string $layerStackClass = Layers::class)
    {
        $this->layers = \array_values($layers);
        $this->layerStackClass = $layerStackClass;
    }

    public function append(string $layer): self
    {
        $this->layers[] = $layer;

        return $this;
    }

    public function prepend(string $layer): self
    {
        \array_unshift($this->layers, $layer);

        return $this;
    }

    public function setResolver($resolver): void
    {
        if (is_object($resolver) && \method_exists($resolver, 'get')) {
            $this->resolver = $resolver;
            return;
        } elseif ($resolver instanceof Closure) {
            $this->resolver = $resolver;
            return;
        }

        throw new \InvalidArgumentException('$resolver must be either a container object or Closure');
    }

    public function run($request, Closure $target)
    {
        /* @var $layers Layer */
        $layers = new $this->layerStackClass($this->layers, $target);

        if (isset($this->resolver)) {
            $layers->setResolver($this->resolver);
        }

        return $layers->next($request);
    }
}
