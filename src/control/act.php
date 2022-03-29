<?php

namespace douggonsouza\mvc\control;

use douggonsouza\mvc\control\exiting;
use douggonsouza\routes\router;
use douggonsouza\propertys\propertysInterface;

abstract class act extends exiting
{
    protected $dir;
    protected $namespace;
    protected $url;

    /**
     * Função a ser executada no contexto da action
     *
     * @param array $info
     * @return void
     */
    abstract public function main(propertysInterface $info);

    /**
     * Para ser disparado antes
     *
     * @return void
     */

    /**
     * Antecede a ação de resposta
     *
     * @param propertysInterface|null $info
     * 
     * @return void
     * 
     */
    public function _before(propertysInterface $info = null)
    {
        return;
    }
}