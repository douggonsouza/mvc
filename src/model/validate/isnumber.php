<?php

namespace douggonsouza\mvc\model\validate;

use douggonsouza\mvc\model\validate\validadeInterface;

class isnumber implements validadeInterface
{
    protected $error;
    
    public function validate($value)
    {
        if(!is_numeric($value)){
            $this->setError('Não é um número.');
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
