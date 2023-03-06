<?php

namespace douggonsouza\benchmarck\blocks;

use douggonsouza\benchmarck\behaviorInterface;

class blocks implements behaviorInterface
{
    /**
     * Executa o comportamento
     *
     * @param string|null $script
     * 
     * @return void
     * 
     */
    public function behavior(string $script = null)
    {
        $dir = __DIR__ . $script;
        if(!file_exists($dir)){
            throw new \Exception("Arquivo não encontrado.");
        }

        return $dir;
    }
}
?>