<?php

namespace douggonsouza\mvc\control;

use douggonsouza\routes\router;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\mvc\control\controllersInterface;
use douggonsouza\mvc\view\screens;
use douggonsouza\mvc\view\views;

class controllers extends screens implements controllersInterface
{
    protected static $autenticate;
    protected static $infos;

    /**
     * Method __construct
     *
     * @return void
     */
    public function __construct(
        string $page = null, 
        string $layout = null
    )
    {
        self::setBenchmarck(router::getBenchmarck());
        self::setPage($page);
        self::setLayout($layout);
        self::setInfos(router::getInfos());
        self::setAutenticate(router::getAutenticate());
    }

    /**
     * Função a ser executada no contexto da action
     *
     * @param array $info
     * @return void
     */
    public function main(propertysInterface $infos = null)
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
    public static function view(string $template = null, propertysInterface $params = null)
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
        return;
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
        return;
    }

    /**
     * Get the value of autenticate
     */ 
    public static function getAutenticate()
    {
        return self::$autenticate;
    }

    /**
     * Set the value of autenticate
     *
     * @return  self
     */ 
    public static function setAutenticate($autenticate)
    {
        if(isset($autenticate) && !empty($autenticate)){
            self::$autenticate = $autenticate;
        }
    }

    /**
     * Get the value of infos
     */ 
    public static function getInfos()
    {
        return self::$infos;
    }

    /**
     * Set the value of infos
     *
     * @return  self
     */ 
    public static function setInfos(propertysInterface $infos)
    {
        if(isset($infos) && !empty($infos)){
            self::$infos = $infos;
        }
    }
}