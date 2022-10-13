<?php

namespace douggonsouza\mvc\control;

use douggonsouza\propertys\propertysInterface;
use douggonsouza\mvc\control\controllersInterface;
use douggonsouza\mvc\view\screens;
use douggonsouza\mvc\view\views;

class controllers extends screens implements controllersInterface
{
    /**
     * Função a ser executada no contexto da action
     *
     * @param array $info
     * @return void
     */
    public function main(propertysInterface $infos)
    {
        return views::identified('', $infos);
    }

    /**
     * Antecede a ação de resposta
     *
     * @param propertysInterface|null $info
     * 
     * @return void
     * 
     */
    public function _after(propertysInterface $info = null)
    {
        return true;
    }

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
        return true;
    }
    
    /**
     * Method request
     *
     * @param string             $function [explicite description]
     * @param propertysInterface $info [explicite description]
     *
     * @return void
     */
    public function roles(string $function, propertysInterface $info = null)
    {
        if(!isset($function) || empty($function)){
            throw new \Exception('Não identificado o parâmetro com o nome da função.');
        }

        if(!$this->_before()){
            throw new \Exception('Checagem anterior à função de resposta inválida.');
        }

        try{
            $this->$function($info);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }

        if(!$this->_after()){
            throw new \Exception('Checagem posterior à função de resposta inválida.');
        }

        return;
    }
}