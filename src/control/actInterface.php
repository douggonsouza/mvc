<?php

namespace douggonsouza\mvc\control;

interface actInterface
{
    /**
     * Para ser disparado antes
     *
     * @return void
     */
    public function _before();


    /**
     * Para ser disparado depois
     *
     * @return void
     */
    public function _after();
}

?>