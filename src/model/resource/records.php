<?php

namespace douggonsouza\mvc\model\resource;

use douggonsouza\mvc\model\resource\recordsInterface;
use douggonsouza\mvc\model\connection\conn;

class records implements recordsInterface
{
    protected $data;
    protected $resource;
    protected $error;
    protected $index = -1;
    protected $isEof = true;
    protected $new   = true;

    public function __construct($resource = null){
        $this->setResource($resource);
    }

    /**
     * Expõe o total de linha afetadas pela query
     * @return int
    */
    public function total()
    {
        if(empty($this->getResource())){
            return 0;
        }

        return mysqli_num_rows($this->getResource());
    }

    /**
     * Exist dados para o recurso
     * @return int
    */
    public function exist()
    {
        if(empty($this->getResource())){
            return false;
        }

        return mysqli_num_rows($this->getResource()) >= 1;
    }
    
    /**
     * Method asAllArray
     *
     * @return void
     */
    public function asAllArray()
    {
        if(empty($this->getResource())){
            return array();
        }

        return mysqli_fetch_all($this->getResource(), MYSQLI_ASSOC);
    }

    /**
     * Atualiza a propriedade data
     *
     * @return bool
     */
    public function data()
    {
        if(empty($this->getResource())){
            return true;
        }

        $data = $this->getResource()->fetch_assoc();
        if(isset($data) && !empty($data)){
            $this->setData($data);
            $this->setIndex($this->getIndex() + 1);
        }
        return true;
    }

    /**
     * Move o ponteiro para o prÃ³ximo
     * 
     */
    public function next()
    {
        if(empty($this->getResource())){
            return true;
        }

        $data = $this->getResource()->fetch_assoc();
        if(!isset($data) || empty($data)){
            $this->setIsEof(true);
            return true;
        }
        $this->setData($data);
        $this->setIndex($this->getIndex() + 1);
        return true;
    }

    /**
     * Move o ponteiro para o anterior
     * 
     */
    public function previous()
    {
        if(empty($this->getResource())){
            return true;
        }

        $this->setIndex($this->getIndex() - 1);
        $this->getResource()->data_seek($this->getIndex());
        $this->data();

        return true;
    }

    /**
     * Move o ponteiro para o primeiro
     * 
     */
    public function first()
    {
        if(empty($this->getResource())){
            return true;
        }

        $this->setIndex(0);
        $this->getResource()->data_seek($this->getIndex());
        $this->data();

        return true;
    }

    /**
     * Move o ponteiro para o Ãºltimo
     * 
     */
    public function last()
    {
        if(empty($this->getResource())){
            return true;
        }

        $this->setIndex($this->total() - 1);
        $this->getResource()->data_seek($this->getIndex());
        $this->data();

        return true;
    }

    /**
     * Popula o objeto data pelo array
     *
     * @param array $data
     * @return bool
     */
    public function populate(array $data)
    {
        if(empty($data)){
            $this->setError('Não é permitido vazio no parâmetro Data.');
            return false;
        }

        foreach($data as $index => $value){
            $this->data[$index] = $value;
        }

        return true;
    }

    /**
     * Get the value of data
     */ 
    public function getData()
    {
        if(!isset($this->data)){
            $this->data = array();
        }
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */ 
    protected function setData($data)
    {
        if(!isset($data) && $this->getIndex() >= $this->total()){
            $this->setIsEof(true);
            return;
        }

        $this->data = $data;
        $this->setIsEof(false);
    }

    /**
     * Get the value of data
     */ 
    public function getField(string $field)
    {
        if(empty($this->getData()) || !isset($field) || empty($field)){
            return null;
        }
        return $this->data[$field];
    }

    /**
     * Preenche um campo com valor
     *
     * @param string $field
     * @param mixed $value
     * @return bool
     */
    public function setField(string $field, $value)
    {
        if(!isset($field) || empty($field) || !isset($value) || empty($value)){
            $this->setError('Não é permitido nulo para os parâmentros Field ou Value.');
            return false;
        }
        if(empty($this->getData())){
            $this->setData(array());
        }
        $this->data[$field] = $value;
        return true;
    }

    /**
     * Get the value of error
     */ 
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @return  self
     */ 
    public function setError(string $error)
    {
        if(isset($error) && !empty($error)){
            $this->error = $error;
        }

        return $this;
    }

    /**
     * Get the value of resource
     */ 
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set the value of resource
     *
     * @return  self
     */ 
    protected function setResource($resource)
    {
        $this->resource = null;
        if(isset($resource) && !empty($resource) && !is_bool($resource)){
            $this->resource = $resource;
            $this->setNew(false);
        }
    }

    /**
     * Get the value of index
     */ 
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the value of index
     *
     * @return  self
     */ 
    public function setIndex($index)
    {
        if(empty($this->getResource())){
            return;
        }

        if(isset($index) && $index <= $this->total()){
            $this->index = $index;
        }
    }

    /**
     * Get the value of isEof
     */ 
    public function getIsEof()
    {
        return $this->isEof;
    }

    /**
     * Set the value of isEof
     *
     * @return  self
     */ 
    public function setIsEof($isEof)
    {
        if(isset($isEof)){
            $this->isEof = $isEof;
        }
    }

    /**
     * Get the value of new
     */ 
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Set the value of new
     *
     * @return  self
     */ 
    public function setNew($new)
    {
        $this->new = $new;
    }
}
