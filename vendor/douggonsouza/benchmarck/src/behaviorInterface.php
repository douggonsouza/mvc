<?php

namespace douggonsouza\benchmarck;

use douggonsouza\propertys\propertysInterface;

interface behaviorInterface
{
    /**
     * Executa o comportamento
     *
     * @param string|null $script
     * 
     * @return void
     * 
     */
    public function behavior(string $script = null);
}