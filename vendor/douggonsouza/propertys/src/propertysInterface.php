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
    public function __construct(array $propertys);

    /**
     * Sobrecarga do método mágico __set
     *
     * @param string $name
     * @param mixed $value
     * 
     */
    public function __set(string $name, mixed $value);

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
    public function toSerializabe()
}
?>