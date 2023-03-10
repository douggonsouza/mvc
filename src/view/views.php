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
            parent::setParams($params);
        }

        // define local block
        if(!self::local($template, 'block')){
            throw new \Exception("Erro durante o carregamento do template");
        }

        return parent::body(self::getTemplateBLock()->getTemplate(), self::getParams());
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
    public static function view(string $template = null, propertysInterface $params = null, string $layout = null)
    {
        // page
        if(isset($template) && !empty($template)){
            self::setPage(($template));
            self::local(self::getPage());
        }

        if(isset($params)){
            parent::setParams($params);
        }

        // layout
        if(isset($layout) && !empty($layout)){
            self::setLayout($layout);
        }
        self::local(self::getLayout(), 'layout');

        return parent::body(self::getTemplateLayout()->getTemplate(), self::getParams());
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
