<?php

namespace douggonsouza\mvc\model\validate;

use douggonsouza\mvc\model\validate\validadeInterface;

class isdatetime implements validadeInterface
{
    protected $error;
    protected $format;

    public function __construct(string $format)
    {
        $this->setFormat($format);
    }
    
    public function validate($value)
    {
        $data = \DateTime::createFromFormat($this->getFormat(), $value);
        if(!$data || $data->format($this->getFormat()) !== $value){
            $this->setError('Não é um e-mail válido.');
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

    /**
     * Get the value of format
     */ 
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the value of format
     *
     * @return  self
     */ 
    public function setFormat($format)
    {
        if(isset($format)){
            $this->format = $format;
        }

        return $this;
    }
}
