<?php

namespace Savks\PhpContexts;

/**
 * @template TContext of Context
 */
abstract class Context
{
    public function wrap(callable $callback): mixed
    {
        return (fn () => $callback())();
    }

    public static function tryUseSelf(): ?static
    {
        return static::tryUse(static::class);
    }

    public static function useSelf(): static
    {
        return static::use(static::class);
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
