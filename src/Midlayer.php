<?php
declare(strict_types=1);

namespace Falgun\Midlayer;

use Closure;
use Falgun\Http\RequestInterface;

final class Midlayer
{

    /** @var array<int, class-string<MiddlewareInterface>> */
    private array $layers;

    /** @var class-string<LayersInterface> */
    private string $layerStackClass;

    /** @var Closure(class-string<MiddlewareInterface>): MiddlewareInterface */
    private Closure $resolver;

    /**
     * @param array<int, class-string<MiddlewareInterface>> $layers
     * @param class-string<LayersInterface> $layerStackClass
     */
    public function __construct(array $layers = [], string $layerStackClass = Layers::class)
    {
        $this->layers = \array_values($layers);
        $this->layerStackClass = $layerStackClass;
        $this->resolver = $this->getDefaultResolver();
    }

    /**
     * @return Closure(class-string<MiddlewareInterface>): MiddlewareInterface
     * @psalm-suppress InvalidStringClass
     */
    private function getDefaultResolver(): Closure
    {
        return function (string $className): MiddlewareInterface {
            return new $className();
        };
    }

    /**
     * @param class-string<MiddlewareInterface> $layer
     * @return \self
     */
    public function append(string $layer): self
    {
        $this->layers[] = $layer;

        return $this;
    }

    /**
     * @param class-string<MiddlewareInterface> $layer
     * @return \self
     */
    public function prepend(string $layer): self
    {
        \array_unshift($this->layers, $layer);

        return $this;
    }

    /**
     * 
     * @param Closure(class-string<MiddlewareInterface>): MiddlewareInterface $resolver
     * @return void
     */
    public function setResolver(Closure $resolver): void
    {
        $this->resolver = $resolver;
    }

    /**
     * @param RequestInterface $request
     * @param Closure(): mixed $target
     * @return mixed
     * @psalm-suppress InvalidStringClass
     */
    public function run(RequestInterface $request, Closure $target)
    {
        /* @var $layers LayersInterface */
        $layers = new $this->layerStackClass($this->layers, $target, $this->resolver);

        return $layers->next($request);
    }
}
