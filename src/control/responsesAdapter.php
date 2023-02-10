<?php

namespace douggonsouza\mvc\control;

use douggonsouza\mvc\control\responsesAdapterInterface;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\benchmarck\benchmarck;
use douggonsouza\benchmarck\benchmarckInterface;

abstract class responsesAdapter implements responsesAdapterInterface
{   
    protected static $controller;

    /**
     * Method way
     *
     * @param controllersAdapterInterface $response [explicite description]
     * @param propertysInterface $infos [explicite description]
     *
     * @return void
     */
    public function way(string $response, propertysInterface $infos)
    {
        return self::getController()->way($response, $infos);
    }
 
    /**
     * Method requestView
     *
     * @param string $template [explicite description]
     * @param propertysInterface $infos [explicite description]
     *
     * @return void
     */
    public static function controllerResponse(string $controller, benchmarck $benchmarck, propertysInterface $infos)
    {
        try{
            // inicia a controller
            if(!isset($controller) || empty($controller)){
                return 404;
            }

            $response = self::withMethod($controller);
            $control = $response[0];


            self::setController(new $control());
            self::getController()->setBenchmarck($benchmarck);
            self::way($response[1], $infos);
        }
        catch(\Exception $e){
            return 500;
        }

        return 200;
    }

    /**
     * Method requestView
     *
     * @param string $template [explicite description]
     * @param propertysInterface $infos [explicite description]
     *
     * @return void
     */
    static public function controllerView(string $response, propertysInterface $infos)
    {
        return self::way($response, $infos);
    }

    /**
     * identifyMethod - Identifica e retorna o metodo
     *
     * @param  string $controller
     * @return string
     */
    protected static function withMethod(string $controller)
    {
        $control = array($controller, 'main');
        if(is_int(strpos($controller, ':'))){
            $control = explode(':', $controller);
        }

        return $control;
    }


    /**
     * Get the value of controller
     */ 
    public function getController()
    {
        return self::$controller;
    }

    /**
     * Set the value of controller
     *
     * @return  self
     */ 
    public static function setController($controller)
    {
        if(isset($controller) && !empty($controller)){
            self::$controller = $controller;
        }
    }
}