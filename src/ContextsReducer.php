<?php

namespace Savks\PhpContexts;

use Closure;

class ContextsReducer
{
    protected array $contexts;

    public function __construct(Context ...$context)
    {
        $this->contexts = $context;
    }

    public function prepend(Context $context): static
    {
        array_unshift($this->contexts, $context);

        return $this;
    }

    public function append(Context $context): static
    {
        $this->contexts[] = $context;

        return $this;
    }

    public function wrap(Closure $callback): mixed
    {
        $resultCallback = array_reduce(
            array_reverse($this->contexts),
            function (Closure $callback, Context $context) {
                return fn () => $context->wrap($callback);
            },
            $callback
        );

        return $resultCallback();
    }
}
