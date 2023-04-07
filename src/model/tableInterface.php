<?php

namespace douggonsouza\mvc\model;

/**
 * designsInterface
 * 
 * @vesion 1.0.0
 * @introduced 2023-04-02 
 */
interface tableInterface
{
    /**
     * Get the value of table
     */ 
    public function getTable();

    /**
     * Get the value of key
     */ 
    public function getKey();
}