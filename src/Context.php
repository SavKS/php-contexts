<?php

namespace Savks\PhpContexts;

use Closure;

abstract class Context
{
    /**
     * @template R
     *
     * @param Closure():R $callback
     *
     * @return R
     */
    public function wrap(Closure $callback): mixed
    {
        return (fn () => $callback())();
    }

    public static function tryUseSelf(bool $withInherited = false): ?static
    {
        return static::tryUse(static::class, $withInherited);
    }

    public static function useSelf(bool $withInherited = false): static
    {
        return static::use(static::class, $withInherited);
    }

    /**
     * @template TContext of Context
     *
     * @param class-string<TContext> $contextFQN
     *
     * @return TContext|null
     */
    public static function tryUse(string $contextFQN, bool $withInherited = false): ?Context
    {
        $backtrace = debug_backtrace();

        foreach ($backtrace as $step) {
            if (! isset($step['object'])) {
                continue;
            }

            if (
                $withInherited
                && is_subclass_of($step['object']::class, $contextFQN)
            ) {
                /** @var TContext */
                return $step['object'];
            } elseif ($contextFQN === $step['object']::class) {
                /** @var TContext */
                return $step['object'];
            }
        }

        return null;
    }

    /**
     * @template TContext of Context
     *
     * @param class-string<TContext> $contextFQN
     *
     * @return TContext
     */
    public static function use(string $contextFQN, bool $withInherited = false): Context
    {
        $context = static::tryUse($contextFQN, $withInherited);

        if (! $context) {
            throw new ContextNotFound($contextFQN);
        }

        return $context;
    }
}
