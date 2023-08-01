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

    public static function tryUseSelf(bool $withInherited = false): ?static
    {
        // @phpstan-ignore-next-line
        return static::tryUse(static::class, $withInherited);
    }

    public static function useSelf(bool $withInherited = false): static
    {
        // @phpstan-ignore-next-line
        return static::use(static::class, $withInherited);
    }

    /**
     * @param class-string<TContext> $contextFQN
     * @return TContext|null
     */
    public static function tryUse(string $contextFQN, bool $withInherited = false): ?Context
    {
        $backtrace = debug_backtrace();

        foreach ($backtrace as $step) {
            if (! isset($step['object'])) {
                continue;
            }

            if ($withInherited && is_subclass_of($step['object']::class, $contextFQN)) {
                return $step['object'];
            } elseif ($step['object']::class === $contextFQN) {
                return $step['object'];
            }
        }

        return null;
    }

    /**
     * @param class-string<TContext> $contextFQN
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
