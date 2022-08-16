<?php

namespace douggonsouza\mvc\control;

use douggonsouza\mvc\control\exiting;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\mvc\control\actInterface;
use douggonsouza\logged\logged;

abstract class act extends exiting implements actInterface
{
    /**
     * Função a ser executada no contexto da action
     *
     * @param array $info
     * @return void
     */
    abstract public function main(propertysInterface $infos);

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