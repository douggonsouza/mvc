<?php

namespace douggonsouza\mvc\model;

use douggonsouza\mvc\model\resource\source;

/**
 * helps
 * 
 * @vesion 1.0.0
 * 2023-04-04 
 */
class helps
{
    protected $columns;

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
     * Method infoColumns - Colhe as informações das colunas da tabela
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
        $source   = new source($sql);
        return $source->close($source->allArray());
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
    
    /**
     * Method dataByColumns
     *
     * @param array $infoColumns [explicite description]
     * @param array $data [explicite description]
     *
     * @return array
     */
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
    
    /**
     * Method filterByColumns
     *
     * @param string $table [explicite description]
     * @param array $data [explicite description]
     *
     * @return void
     */
    protected function filterByColumns(string $table, array $data)
    {
        $content = array();

        if(!isset($data) || empty($data)){
            return $content;
        }

        // info das colunas
        if(!isset($this->columns)){
            $this->setColumns($this->infoColumns($this->getTable()));
        }

        // array do conteúdo
        foreach ($this->getColumns() as $item) {
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

    /**
     * Cria query de Save
     *
     * @param array $data
     * @return string
     */
    public function queryForSave(array $data, string $table, bool $isNew = true)
    {
        if (!isset($data) || empty($data)) {
            throw new \Exception('Não é permitido parâmetro data nulo.');
        }

        // info das colunas
        if(!isset($this->columns)){
            $this->setColumns($this->infoColumns($table));
        }

        $where = null;

        // array do conteúdo

        $content = array();
        foreach ($this->getColumns() as $item) {
            if ($item['Key'] == 'PRI') {
                if (isset($data[$item['Field']]) && $isNew == false) {
                    $where = $item['Field'] . ' = ' . $this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    );
                }
                continue;
            }

            if (isset($where)) {
                $content[$item['Field']] = $item['Field'] . ' = ' . $this->prepareValueByColumns(
                    $this->type($item['Type']),
                    $data[$item['Field']]
                ) . '';
                continue;
            }

            // active
            if (isset($item['Field']) && $item['Field'] == 'active') {
                if (isset($data[$item['Field']])) {
                    $content[$item['Field']] = $item['Field'] . ' = ' . $this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ) . '';
                }
                continue;
            }
            // created
            if (isset($item['Field']) && $item['Field'] == 'created') {
                $content[$item['Field']] = 'NOW()';
                if (isset($data[$item['Field']])) {
                    $content[$item['Field']] = $item['Field'] . ' = ' . $this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ) . '';
                }
                continue;
            }
            // modified
            if (isset($item['Field']) && $item['Field'] == 'modified') {
                $content[$item['Field']] = 'NOW()';
                if (isset($data[$item['Field']])) {
                    $content[$item['Field']] = $item['Field'] . ' = ' . $this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ) . '';
                }
                continue;
            }
            // removed
            if (isset($item['Field']) && $item['Field'] == 'removed') {
                $content[$item['Field']] = 1;
                if (isset($data[$item['Field']])) {
                    $content[$item['Field']] = $item['Field'] . ' = ' . $this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ) . '';
                }
                continue;
            }

            $content[$item['Field']] = $this->prepareValueByColumns(
                $this->type($item['Type']),
                $data[$item['Field']]
            );
        }

        // update
        if (isset($where)) {
            $sql = sprintf(
                "UPDATE %1\$s SET %2\$s WHERE %3\$s;",
                $table,
                implode(', ', $content),
                $where
            );
            return $sql;
        }
        // save
        $sql = sprintf(
            "INSERT INTO %1\$s (%2\$s) VALUES (%3\$s);",
            $table,
            implode(', ', array_keys($content)),
            implode(', ', $content),
        );
        return $sql;
    }

    /**
     * Cria query de Deleção
     *
     * @param array $data
     * @return string
     */
    public function queryForDelete(array $data, string $table, string $key)
    {
        if (!isset($data) || empty($data)) {
            throw new \Exception('Nâo é permitido parâmetro data nulo.');
        }

        // info das colunas
        if(!isset($this->columns)){
            $this->setColumns($this->infoColumns($table));
        }

        // existe id
        $where = null;
        $infoKey = $this->infoColumns($table, $key);
        if (isset($data[$infoKey['Field']])) {
            $where = $infoKey['Field'] . ' = ' . $this->prepareValueByColumns(
                $this->type($infoKey['Type']),
                $data[$infoKey['Field']]
            );
        }
        if (!isset($where)) {
            throw new \Exception('Não é possível deletar um novo records.');
        }

        // update
        $sql = sprintf(
            "DELETE FROM %1\$s WHERE %2\$s;",
            $table,
            $where
        );
        return $sql;
    }

    /**
     * Method queryForSoftDelete - Cria query de inativação
     *
     * @param array $data
     * @param int $removed
     *
     * @return string
     */
    public function queryForSoftDelete(array $data, string $table, string $key, int $removed = 1)
    {
        if (!isset($data) || empty($data)) {
            throw new \Exception('Nâo é permitido parâmetro data nulo.');
        }

        // existe id
        $where = null;
        $infoKey = $this->infoColumns($table, $key);
        if (isset($data[$infoKey['Field']])) {
            $where = $infoKey['Field'] . ' = ' . $this->prepareValueByColumns(
                $this->type($infoKey['Type']),
                $data[$infoKey['Field']]
            );
        }
        if (!isset($where)) {
            throw new \Exception('Não é possível deletar um novo records.');
        }

        // update
        $sql = sprintf(
            "UPDATE FROM %1\$s SET active='no', modified=NOW(), removed=%2\$d WHERE %3\$s;",
            $table,
            $removed,
            $where
        );
        return $sql;
    }

    /**
     * Get the value of columns
     */ 
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set the value of columns
     *
     * @return  self
     */ 
    protected function setColumns($columns)
    {
        if(isset($columns) && !empty($columns)){
            $this->columns = $columns;
        }

        return $this;
    }
}