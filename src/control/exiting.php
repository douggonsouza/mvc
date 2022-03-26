<?php

namespace douggonsouza\mvc\control;

use douggonsouza\mvc\view\view;

class exiting extends view
{
    const TYPE_JSON = 1;
    const TYPE_FILE = 2;
    const TYPE_TEMPLATE = 3;
    const TYPE_PARTIAL = 4;

    const LIST_TYPE = array(
        self::TYPE_JSON => 'json',
        self::TYPE_FILE => 'file',
        self::TYPE_TEMPLATE => 'template',
        self::TYPE_PARTIAL => 'partial'
    );

    protected $file;
    protected $type;
    protected $params = array();

    /**
     * Evento construtor da classe
     *
     * @param string|null $file
     * @param int|null $type
     * 
     * @return void
     */
    public function __construct($file = null, $type = null)
    {
        $this->init($file, $type);
    }

    /**
     * Undocumented function
     *
     * @param string $file
     * @param int    $type
     * @return void
     */
    public function init($file = null, $type = null, $controllerType = self::TYPE_TEMPLATE)
    {
        try{
            $this->setFile($file);
            $this->setType($type);
            return true;
        }
        catch(\Exception $e){
            return false;
        }
    }

    /**
     * Executa a saída conforme os parametros passados
     *
     * @param array  $params
     * @param int    $type
     * @param string $file
     * @return bool;
     */
    final public function exit($params = array(), $type = self::TYPE_TEMPLATE, $file = null)
    {
        $this->init($file, $type);
        
        try{
            switch($this->getType()){
                case self::TYPE_TEMPLATE:
                    if(empty($this->getTemplate())){
                        throw new \Exception('Não existe template para ser carregado.');
                    }
                    if(!empty($this->getFile())){
                        $this->setTemplate($this->getFile());
                    }
                    $this->view($this->getParams());
                    break;
                case self::TYPE_PARTIAL:
                    if(empty($this->getLayout())){
                        throw new \Exception('Não existe o layout a ser carregado.');
                    }
                    if(!empty($this->getFile())){
                        $this->setLayout($this->getFile());
                    }
                    $this->view($this->getParams());
                    break;
                case self::TYPE_JSON:
                    $this->setParams($params);
                    if(empty($this->getParams())){
                        throw new \Exception('Não existe informações para serem enviadas.');
                    }
                    $this->json($this->getParams());
                    break;
                case self::TYPE_FILE:
                    if(empty($this->getFile())){
                        throw new \Exception('Não existe caminho de Requisição.');
                    }
                    $this->file($this->getFile());
                    break;
                default:
                    throw new \Exception('Não configurado o Tipo de saída.');
            }
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        if(isset($type) && !empty($type)){
            $this->type = $type;
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
            if(is_array($params)){
                $this->params = array_merge($params, $this->param);
                return $this;
            }
            $this->params[] = $params;
        }
        return $this;
    }
}