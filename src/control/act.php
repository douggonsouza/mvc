<?php

namespace douggonsouza\mvc\control;

use douggonsouza\mvc\control\exiting;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\benchmarck\benchmarckInterface;
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
    
    /**
     * Method view - Carrega a page e o layout conforme endereço
     *
     * @param string $view
     * @param propertysInterface $info 
     * @param string $layout 
     *
     * @return void
     */
    public function view(string $view, propertysInterface $info = null, string $layout = null)
    {
        if(isset($info)){
            $this->setPropertys($info);
        }

        if(isset($layout)){
            if(!file_exists($layout)){
                throw new \Exception('Layout não encontrado.');
            }
            $this->setLayout($layout);
        }

        if(isset($view)){
            if(!file_exists($view)){
                throw new \Exception('Page não encontrada.');
            }
            $this->setPage($view);
        }

        parent::body($this->getLayout(), $this->getPropertys());
        return;
    }
}