<?php

namespace douggonsouza\mvc\control;

use douggonsouza\propertys\propertysInterface;

interface controllersInterface
{
    /**
     * Função a ser executada no contexto da action
     *
     * @param array $info
     * @return void
     */
    public function main(propertysInterface $infos = null);
}

?>