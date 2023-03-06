<?php

namespace douggonsouza\propertys;

interface propertysInterface
{
    /**
     * Evento construtor da classe
     *
     * @param array $propertys
     * 
     */
    public function __construct(array $propertys = null);

    /**
     * Sobrecarga do método mágico __set
     *
     * @param string $name
     * @param mixed $value
     * 
     */
    public function __set(string $name, mixed $value);

    /**
     * Adiciona propriedades
     *
     * @param array $propertys
     * 
     * @return self
     * 
     */
    public function add(array $propertys);

    /**
     * Deleta propriedade
     *
     * @param string $property
     * 
     * @return self
     * 
     */
    public function del(string $property);

    /**
     * Exporta classe como array
     *
     * @return array
     * 
     */
    public function toArray();

    /**
     * Exporta classe como serialize
     *
     * @return string
     * 
     */
    public function toSerializabe();
}
?>