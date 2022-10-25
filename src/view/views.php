<?php

namespace douggonsouza\mvc\view;

use douggonsouza\benchmarck\behaviorInterface;
use douggonsouza\mvc\view\screens;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\mvc\view\viewsInterface;
use douggonsouza\mvc\view\templates;

abstract class views extends screens implements viewsInterface
{
    protected $file;
    
    /**
     * Responde a requisição com um array do tipo json
     * 
     * @param array $params
     * 
     * @return void
     */
    static public function json(array $params = array())
    {
        if(!isset($params) || empty($params)){
            header('Content-Type: application/json');
            exit(json_encode(array()));
        }

        header('Content-Type: application/json');
        exit(json_encode($params));
        return;
    }
 
    /**
     * Method block
     *
     * @param string             $template [explicite description]
     * @param propertysInterface $params   [explicite description]
     *
     * @return void
     */
    public static function block(string $template, propertysInterface $params = null)
    {
        if(isset($params)){
            parent::setPropertys($params);
        }

        self::setTemplateBlock(
            new templates(self::getBenchmarck()->identified($template), 'block')
        );

        return parent::body(self::getTemplateBLock()->getTemplate(), self::getPropertys());
    }

    /**
    * Carrega o local da identificação da requisição
    *
    * @param string                  $template
    * @param behaviorInterface       $config
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    public static function view(string $template, propertysInterface $params = null)
    {
        if(is_null(self::getLayout())){
            throw new \Exception('Não identificado o layout.');
        }

        if(isset($params)){
            parent::setPropertys($params);
        }
        self::setPage(self::getBenchmarck()->identified($template));

        self::setTemplateLayout(
            new templates(self::getBenchmarck()->identified(self::getLayout()), 'layout')
        );
        self::setTemplatePage(
            new templates(self::getBenchmarck()->identified($template), 'page')
        );

        return parent::body(self::getTemplateLayout()->getTemplate(), self::getPropertys());
    }

    /**
     * Get the value of file
     */ 
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return  self
     */ 
    public function setFile($file)
    {
        if(isset($file) && !empty($file)){
        $this->file = $file;
        }
        return $this;
    }
}        
