<?php

namespace douggonsouza\mvc\model;

use douggonsouza\mvc\model\tableInterface;
use douggonsouza\mvc\model\config;

abstract class configs
{
    public static $config;

    /**
     * Method get
     *
     * @param string $name [explicite description]
     *
     * @return string|void
     */
    public static function get(string $name)
    {
        self::setConfig(new config(['name' => $name]));

        if(self::getConfig()->isModel()){
            return self::getConfig()->get('content');
        }
        
        return null;
    }

    /**
     * Method set
     *
     * @param string $name [explicite description]
     * @param string $content [explicite description]
     *
     * @return bool
     */
    public static function set(string $name, string $content)
    {
        if(!isset($name) || !isset($content)){
            throw new \Exception("Não existe parâmetro nome ou conteúdo.");
        }

        $config = new config();
        $config->populate([
            'name' => $name,
            'content' => $content
        ]);
        
        return $config->save();
    }

    /**
     * Get the value of config
     */ 
    public static function getConfig()
    {
        return self::$config;
    }

    /**
     * Set the value of config
     *
     * @return  self
     */ 
    protected static function setConfig(tableInterface $config)
    {
        if(isset($config) && !empty($config)){
            self::$config = $config;
        }
    }
}

?>