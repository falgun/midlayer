<?php
declare(strict_types=1);

namespace Falgun\Midlayer;

use Closure;

class Layers implements LayersInterface
{

    protected array $layers;
    protected int $index;
    protected Closure $target;
    protected $resolver;

    public function __construct(array $layers, Closure $target)
    {
        $this->layers = $layers;
        $this->target = $target;
        $this->index = -1;
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

    public function next($request)
    {
        $this->moveToNextLayer();

        $className = $this->getCurrentLayer();

        if ($className === null) {
            return ($this->target)();
        }

        $middleware = $this->resolveClass($className);

        return $middleware->handle($request, $this);
    }

    protected function resolveClass(string $className)
    {
        if (\is_object($this->resolver)) {
            return $this->resolver->get($className);
        } elseif ($this->resolver instanceof Closure) {
            return ($this->resolver)($className);
        }

        return (new $className());
    }

    protected function getCurrentLayer()
    {
        return $this->layers[$this->index] ?? null;
    }

    protected function moveToNextLayer(): self
    {
        $this->index++;
        return $this;
    }
}
