<?php

namespace douggonsouza\mvc\model\validate;

use douggonsouza\mvc\model\validate\validadeInterface;

class limit implements validadeInterface
{
    protected $error;
    protected $limit;

    public function __construct($limit)
    {
        $this->setLimit($limit);
    }
    
    public function validate($value)
    {
        if(strlen($value) > $this->getLimit()){
            $this->setError('O valor excede o tamanho do campo.');
        }

        return $this;
    }

    /**
     * Get the value of limit
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        if(isset($limit)){
            $this->limit = $limit;
        }

        return $this;
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
    public function setError($error)
    {
        if(isset($error) && !empty($error)){
            if(!is_array($this->getError())){
                $this->error = array();
            }

            if(is_array($error)){
                $this->error = array_merge($this->error,$error);
                return $this;
            }
            
            $this->error[] = $error;
        }
        return $this;
    }
}
