<?php

namespace douggonsouza\benchmarck;

use douggonsouza\benchmarck\behaviorInterface;

class identify implements behaviorInterface
{
    protected $config = array();

        
    /**
     * Evento construtor da classe
     *
     * @param  mixed $config
     * @return void
     */
    public function __construct(string $config = null)
    {
        $this->config($config);
    }
    
    /**
     * Recebe config default
     *
     * @param  mixed $config
     * @return void
     */
    public function config(string $config = null)
    {
        if(!file_exists($config)){
            return false;
        }

        $this->config = include($config);

        return true;
    }

    /**
     * Executa o comportamento
     *
     * @param string|null $script
     * 
     * @return void
     * 
     */
    public function behavior(string $identity = null)
    {
        $dir = '';
        if(isset($identity) && !empty($identity)){
            $dir = $this->local($identity);
        }
        if(!file_exists($dir)){
            throw new \Exception("Arquivo não encontrado.");
        }

        return $dir;
    }

    /** 
     * Adiciona controller ou local ao identificador do template
     * 
     * @param string $identify
     * @param string $local
     * @param string $controller
     * 
     * @return bool
     */
    public static function add(string $identify, string $local = null, string $controller = null)
    {
        return self::setConfig($identify, $controller, $local);
    }

    /** 
     * Adiciona controller ou local ao identificador do template
     * 
     * @param string $identify
     * @param string $local
     * @param string $controller
     * 
     * @return bool
     */
    public static function addController(string $identify, string $controller = null)
    {
        return self::setConfig($identify, $controller);
    }

    /** 
     * Devolve o local para do identificador
     * 
     * @param string $identify
     * 
     * @return string|null
     */
    public function local(string $identify)
    {
        $config = $this->getConfig()[$identify];
        if(isset($config) && !empty($config)){
            return $config['local'];
        }
        return null;
    }

    /** 
     * Devolve o controller para do identificador
     * 
     * @param string $identify
     * 
     * @return string|null
     */
    public function controller(string $identify)
    {
        $config = $this->getConfig()[$identify];
        if(isset($config) && !empty($config)){
            return $config['controller'];
        }
        return null;
    }

    /**
     * Get the value of config
     */ 
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the value of config
     *
     * @return  bool
     */ 
    public function setConfig($identify, string $controller = null, string $local = null)
    {
        if(!isset($identify) || empty($identify)){
            return false;
        }

        $config = $this->getConfig();

        if(isset($config[$identify]) && !empty($config[$identify])){
            $config = array(
                'local' => $local,
                'controller' => $controller
            );
            return true;
        }

        $this->config[$identify] = array(
            'local' => $local,
            'controller' => $controller
        );

        return true;
    }
}
?>