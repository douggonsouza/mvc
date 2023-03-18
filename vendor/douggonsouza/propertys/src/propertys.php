<?php

namespace douggonsouza\propertys;

use stdClass;
use douggonsouza\propertys\propertysInterface;

/**
 * propertys: Conteiner de propriedades
 */
class propertys extends stdClass implements propertysInterface
{
    /**
     * __construct: Evento construtor da classe
     *
     * @param array $propertys
     * 
     */
    public function __construct(array $propertys = null)
    {
        if(isset($propertys)){
            $this->add($propertys);
        }
    }

    /**
     * add: Adiciona propriedades
     *
     * @param array $propertys
     * 
     * @return self
     * 
     */
    public function add(array $propertys)
    {
        foreach($propertys as $name => $value){
            if(isset($value) && !empty($value)){
                $this->$name = $value;
            }
        }

        return $this;
    }

    /**
     * del: Deleta propriedade
     *
     * @param string $property
     * 
     * @return self
     * 
     */
    public function del(string $property)
    {
        unset($this->$property);

        return $this;
    }

    /**
     * Sobrecarga do método mágico __set
     *
     * @param string $name
     * @param mixed $value
     * 
     */
    public function __set(string $name, $value)
    {
        if(isset($value) && !empty($value)){
            $this->$name = $value;
        }
    }

    /**
     * toArray: Exporta classe como array
     *
     * @return array
     * 
     */
    public function toArray()
    {
        return (array) $this;
    }

    /**
     * toSerializabe: Exporta classe como serialize
     *
     * @return string
     * 
     */
    public function toSerializabe()
    {
        return serialize($this);
    }
    
    /**
     * Method exist
     *
     * @return void
     */
    public function exist()
    {
        $propertys = get_object_vars($this);
        return isset($propertys) && !empty($propertys)? true: false;
    }
}
?>