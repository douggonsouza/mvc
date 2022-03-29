<?php

namespace douggonsouza\mvc\view;

use douggonsouza\mvc\view\display;
use douggonsouza\propertys\propertysInterface;

class view extends display
{
    protected $layout   = null;
    protected $template = null;
    protected $file     = null;
    protected $params   = null;

    /**
    * Carrega o layout da requisição
    *
    * @param string $layout
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    final public function layout(string $layout, propertysInterface $params = null)
    {
        $this->setParams($params);
        $this->setLayout($params);
        parent::body(
            $this->getLayout(),
            $this->getParams()
        );

        return;
    }

    /**
     * Carrega o template parcial da requisição
     *
    * @param string $layout
    * @param propertysInterface|null $params
    * 
    * @return void
     * 
     */
    public function template(string $layout, propertysInterface $params = null)
    {
        $this->setParams($params);                   
        parent::body(
            $this->getTemplate(),
            $this->getParams()
        );
        return;
	}
    
    /**
     * Responde a requisição com um array do tipo json
     * @param array $variables
     */
    final public function json($params)
    {
        if(!isset($params) || empty($params)){
            throw new \Exception("Parameters JSON not found.");
        }
        header('Content-Type: application/json');
        exit(json_encode($params));
    }

    /**
     * Responde a requisição com um arquivo
     * @param array $variables
     */
    final public function file($localRequest)
    {
        $this->setTemplate($localRequest);                   
        exit(parent::render($this->getTemplate()));
    }

    /**
     * Get the value of template
     */ 
    public function getTemplate()
    {
            return $this->template;
    }

    /**
     * Set the value of template
     *
     * @return  self
     */ 
    public function setTemplate($template)
    {
        if(isset($template) && !empty($template))
            $this->template = $this->existExtensionTemplate($template);
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
     * Get the value of params
     */ 
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the value of params
     *
     * @return  self
     */ 
    public function setParams($params)
    {
        if(isset($params) && !empty($params)){
            $this->params = $params;
        }

        return $this;
    }
}        
