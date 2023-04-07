<?php

namespace douggonsouza\mvc\model;

use douggonsouza\mvc\model\table;
use douggonsouza\mvc\model\tableInterface;

class config extends table implements tableInterface
{
    public $table = 'configs';
    public $key   = 'config_id';
    public $options = "SELECT value, name as label FROM configs %s;";

    /**
     * options
     *
     * @return void
     */
    public function options(array $where = null)
    {
        return parent::options($where);
    }

    /**
     * Get the value of table
     */ 
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get the value of key
     */ 
    public function getKey()
    {
        return $this->key;
    }
}

?>