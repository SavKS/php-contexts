<?php

namespace Savks\PhpContexts;

use Closure;

/**
 * @template TContext of \Savks\PhpContexts\Context
 */
abstract class Context
{
    public function wrap(callable $callback): void
    {
        Closure::bind($callback, $this)();
    }

    /**
     * @param class-string<TContext> $contextFQN
     * @return TContext|null
     */
    public static function tryUse(string $contextFQN): ?Context
    {
        $backtrace = \debug_backtrace();

        foreach ($backtrace as $step) {
            if (isset($step['object']) && $step['object']::class === $contextFQN) {
                return $step['object'];
            }
        }

        return null;
    }

    /**
     * @param class-string<TContext> $contextFQN
     * @return TContext
     */
    public static function use(string $contextFQN): Context
    {
        $context = static::tryUse($contextFQN);

        if (! $context) {
            throw new ContextNotFound($contextFQN);
        }

        return $context;
    }
}
