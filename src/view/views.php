<?php

namespace douggonsouza\mvc\view;

use douggonsouza\benchmarck\behaviorInterface;
use douggonsouza\mvc\view\screens;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\benchmarck\identify;
use douggonsouza\mvc\view\viewsInterface;

abstract class views extends screens implements viewsInterface
{
    protected $file;
    protected $propertys;
    

    /**
    * Carrega o layout da requisição
    *
    * @param string $layout
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    static public function layout(string $layout, propertysInterface $params)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }
        parent::body($this->getBenchmarck()->layouts($layout), $this->getPropertys());
        return;
    }

    /**
    * Recebe objeto de referência
    *
    * @param mixed $benchmarck
    * 
    * @return void
    * 
    */
    static public function benchmarck($benchmarck)
    {
        $this->setBenchmarck($benchmarck);
    }

    /**
    * Carrega o block da requisição
    *
    * @param string $block
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    static public function block(string $block, propertysInterface $params = null)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }
        parent::body($this->getBenchmarck()->blocks($block), $this->getPropertys());
        return;
    }

    /**
    * Carrega a Page Content da requisição
    *
    * @param string $page
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    static public function page(string $page, propertysInterface $params = null)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }
        parent::body($page, $this->getPropertys());
        return;
    }
    
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
    * Carrega o local da identificação da requisição
    *
    * @param string                  $identify
    * @param behaviorInterface       $config
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    static public function identified(string $identify, propertysInterface $params = null)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }
        parent::body($this->getBenchmarck()->identified($identify), $this->getPropertys());
        return;
    }

    /**
     * Responde a requisição com um arquivo
     * @param array $variables
     */
    // final public function file($localRequest)
    // {
    //     $this->setTemplate($localRequest);                   
    //     exit(parent::render($this->getTemplate()));
    // }

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

    /**
     * Get the value of propertys
     */ 
    public function getPropertys()
    {
        return $this->propertys;
    }

    /**
     * Set the value of propertys
     *
     * @return  self
     */ 
    public function setPropertys(propertysInterface $propertys)
    {
        if(isset($propertys) && !empty($propertys)){
            $this->propertys = $propertys;
        }

        return $this;
    }
}        
