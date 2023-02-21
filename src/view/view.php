<?php

namespace douggonsouza\mvc\view;

use douggonsouza\benchmarck\behaviorInterface;
use douggonsouza\mvc\view\display;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\benchmarck\benchmarckInterface;
use douggonsouza\benchmarck\identify;

class view extends display
{
    protected $file;
    protected $propertys;
    protected $benchmarck;
    protected $page;
    protected $block;
    protected $layout;
    protected $identifyLayout;

    /**
    * Carrega o layout da requisição
    *
    * @param string $layout
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    final public function layout(string $layout, propertysInterface $params)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }

        $this->setLayout($this->getBenchmarck()->layouts($layout));

        parent::body($this->getLayout(), $this->getPropertys());
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
    public static function benchmarck(benchmarckInterface $benchmarck)
    {
        self::setBenchmarck($benchmarck);
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
    final public function block(string $block, propertysInterface $params = null)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }

        $this->setBlock($this->getBenchmarck()->blocks($block));

        parent::body($this->getBlock(), $this->getPropertys());
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
    final public function page(string $page, propertysInterface $params = null)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }

        $this->setPage($page);

        parent::body($this->getPage(), $this->getPropertys());
        return;
    }
    
    /**
     * Responde a requisição com um array do tipo json
     * 
     * @param array $params
     * 
     * @return void
     */
    final public function json(array $params = array())
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
    final public function identified(string $identify, propertysInterface $params = null, string $layout = null)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }

        $this->identifyLayout($layout);
        $this->setPage($this->getBenchmarck()->identified($identify));

        parent::body($this->getLayout(), $this->getPropertys());
        return;
    }

    /**
     * Set the value of identifyLayout
     *
     * @return  self
     */ 
    public function identifyLayout(string $identifyLayout = null)
    {
        if(isset($identifyLayout) && !empty($identifyLayout)){
            $this->setIdentifyLayout($identifyLayout);
        }

        if(isset($this->identifyLayout)){
            $this->setlayout($this->getBenchmarck()->identified($this->getIdentifyLayout()));
        }

        return $this;
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


    /**
     * Get the value of benchmarck
     */ 
    public function getBenchmarck()
    {
        return $this->benchmarck;
    }

    /**
     * Set the value of benchmarck
     *
     * @return  self
     */ 
    public function setBenchmarck(benchmarckInterface $benchmarck)
    {
        if(isset($benchmarck) && !empty($benchmarck)){
            $this->benchmarck = $benchmarck;
        }

        return $this;
    }

    /**
     * Get the value of page
     */ 
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set the value of page
     *
     * @return  self
     */ 
    public function setPage($page)
    {
        if(isset($page) && !empty($page)){
            $this->page = $page;
        }

        return $this;
    }

    /**
     * Get the value of layout
     */ 
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set the value of layout
     *
     * @return  self
     */ 
    public function setLayout($layout)
    {
        if(isset($layout) && !empty($layout)){
            $this->layout = $layout;
        }

        return $this;
    }

    /**
     * Get the value of block
     */ 
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set the value of block
     *
     * @return  self
     */ 
    public function setBlock($block)
    {
        if(isset($block) && !empty($block)){
            $this->block = $block;
        }

        return $this;
    }

    /**
     * Get the value of identifyLayout
     */ 
    public function getIdentifyLayout()
    {
        return $this->identifyLayout;
    }

    /**
     * Set the value of identifyLayout
     *
     * @return  self
     */ 
    public function setIdentifyLayout($identifyLayout)
    {
        if(isset($identifyLayout) && !empty($identifyLayout)){
            $this->identifyLayout = $identifyLayout;
        }

        return $this;
    }
}        
