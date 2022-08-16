<?php

namespace douggonsouza\mvc\model;

class utils
{
    /**
     * Exporta o valor conforme o seu tipo SQL
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    public function prepareValueByColumns(string $type, $value)
    {
        if(!isset($type) || empty($type)){
            return 'NULL';
        }

        if(!isset($value) || empty($value)){
            return 'NULL';
        }

        switch(strtolower($type)){
            case 'integer':
            case 'int':
            case 'float':
            case 'double';
            case 'decimal':
            case 'numeric':
                return $value;
            break;
            case 'varchar':
            case 'char':
            case 'date':
            case 'datetime':
            case 'enum':
                return "'".$value."'";
            break;
            default:
                return $value;
        }
    }

    /**
     * Limit conforme o tipo do campo
     *
     * @param string $type
     * @return int
     */
    protected function limit($type)
    {
        $limit = (int) preg_replace("/[^0-9]/", "", $type);
        if(stristr($type, 'decimal') || stristr($type, 'numeral')){
            if(preg_match("/[0-9]{1,}/", $type, $match)){
                $limit = (int) $match[0];
            }
        }
        return $limit != 0? $limit: 255; 
    }

    /**
     * Type conforme o tipo do campo
     *
     * @param string $type
     * @return int
     */
    protected function type($type)
    {
        if(!isset($type) || empty($type)){
            return null;
        }

        return (explode('(', $type))[0];
    }
}