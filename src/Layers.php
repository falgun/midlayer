<?php
declare(strict_types=1);

namespace Falgun\Midlayer;

use Closure;
use Falgun\Http\RequestInterface;

final class Layers implements LayersInterface
{

    /** @var array<int, class-string<MiddlewareInterface>> */
    private array $layers;
    private int $index;

    /** @var Closure(): mixed */
    private Closure $target;

    /** @var Closure(class-string<MiddlewareInterface>): MiddlewareInterface */
    private Closure $resolver;

    /**
     * @param array<int, class-string<MiddlewareInterface>> $layers
     * @param Closure(): mixed $target
     * @param Closure(class-string<MiddlewareInterface>): MiddlewareInterface $resolver
     */
    public function __construct(array $layers, Closure $target, Closure $resolver)
    {
        $this->layers = $layers;
        $this->target = $target;
        $this->index = -1;
        $this->resolver = $resolver;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function next(RequestInterface $request)
    {
        $this->moveToNextLayer();

        $className = $this->getCurrentLayer();

        if ($className === null) {
            return ($this->target)();
        }

        $middleware = $this->resolveClass($className);

        return $middleware->handle($request, $this);
    }

    /**
     * @param class-string<MiddlewareInterface> $className
     * @return MiddlewareInterface
     * @psalm-suppress InvalidStringClass
     */
    private function resolveClass(string $className): MiddlewareInterface
    {
        return ($this->resolver)($className);
    }

    /**
     * @return class-string<MiddlewareInterface>|null
     */
    private function getCurrentLayer()
    {
        return $this->layers[$this->index] ?? null;
    }

    private function moveToNextLayer(): self
    {
        $this->index++;
        return $this;
    }
}
