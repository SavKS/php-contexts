<?php

namespace Savks\PhpContexts;

use RuntimeException;

class ContextNotFound extends RuntimeException
{
    public function __construct(string $contextFQN)
    {
        parent::__construct('The code was executed outside the "' . $contextFQN . '" context');
    }
}
