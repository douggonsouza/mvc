<?php

namespace douggonsouza\mvc\model;

use douggonsouza\mvc\model\connection\conn;
use douggonsouza\mvc\model\resource\source;
use douggonsouza\mvc\model\resource\sourceInterface;

/**
 * helps
 * 
 * @vesion 1.0.0
 * 2023-04-04 
 */
class helps
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

    /**
     * Method infoColumns - Colhe as informações para as colunas da tabela
     *
     * @param string $table [explicite description]
     * @param $field $field [explicite description]
     *
     * @return array
     */
    protected function infoColumns(string $table, $field = null)
    {
        if (!isset($table) || empty($table)) {
            return null;
        }

        // gerar sql
        $sql = null;
        $sql = sprintf('SHOW COLUMNS FROM %s', $table);
        if (isset($field)) {
            $sql = sprintf(
                "SHOW COLUMNS FROM %s WHERE Field='%s'",
                $table,
                $field
            );
        }

        // devolve array
        $source   = new source(conn::select($sql));
        $allArray = $source->allArray();
        $source->close();
        return $allArray;
    }

    /**
     * Method prepareArrayWhere
     *
     * @return array
     */
    protected function prepareArrayWhere(string $table, array $where)
    {
        if(empty($where)){
            throw new \Exception("Não existe parâmetro Where.");
        }

        $content = $this->filterByColumns($table, $where);
        array_walk($content, function (&$item, $key) {
            $item = $key . ' = ' . $item;
        });

        return $content;
    }


    /**
     * Devolve sql para a realização da busca
     *
     * @param array $where
     * @return string
     */
    public function sqlSeek(string $table, array $where = null)
    {
        if (empty($table)) {
            return null;
        }

        if (!isset($where)) {
            $where = array($table . '.active = 1');
        }

        return sprintf(
            'SELECT * FROM %1$s WHERE %2$s;',
            $table,
            implode(' AND ', $where)
        );
    }

    protected function dataByColumns(array $infoColumns, array $data)
    {
        $content = array();

        if (!isset($infoColumns) || empty($infoColumns) || !isset($data) || empty($data)) {
            return $content;
        }

        // array do conteúdo
        foreach ($infoColumns as $item) {
            if (isset($data[$item['Field']])) {
                $content[$item['Field']] = trim($data[$item['Field']]);
            }
        }

        return $content;
    }

    protected function filterByColumns(string $table, array $data)
    {
        $content = array();
        $infoColumns = $this->infoColumns($table);
        if (!isset($infoColumns) || empty($infoColumns) || !isset($data) || empty($data)) {
            return $content;
        }

        // array do conteúdo
        foreach ($infoColumns as $item) {
            if (isset($data[$item['Field']])) {
                $limit = $this->limit($item['Type']);
                $content[$item['Field']] = trim(
                    $this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    )
                );
            }
        }

        return $content;
    }
}