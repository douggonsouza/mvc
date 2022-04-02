<?php

namespace douggonsouza\mvc\control;

use douggonsouza\propertys\propertysInterface;

interface actInterface
{
    /**
     * Para ser disparado antes
     *
     * @return void
     */
    public function _before();

    /**
     * Função a ser executada no contexto da action
     *
     * @param array $info
     * @return void
     */
    public function main(propertysInterface $infos);
}

?>