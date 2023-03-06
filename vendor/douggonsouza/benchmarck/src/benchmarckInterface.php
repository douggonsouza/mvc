<?php

namespace douggonsouza\benchmarck;

use douggonsouza\propertys\propertysInterface;

interface benchmarckInterface
{
    /**
     * Executa o comportamento
     *
     * @param propertysInterface|null $propertys
     * 
     * @return void
     * 
     */
    public function behavior(propertysInterface $propertys = null);
}