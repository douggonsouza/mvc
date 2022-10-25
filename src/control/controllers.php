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
        return self::view('', $infos);
    }
    
    /**
     * Method view
     *
     * @param string             $template [explicite description]
     * @param propertysInterface $params   [explicite description]
     *
     * @return void
     */
    public static function view(string $template, propertysInterface $params = null)
    {
        return views::view($template, $params);
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
}