<?php

namespace douggonsouza\mvc\model;

use douggonsouza\mvc\model\resource\resource;
use douggonsouza\mvc\model\modelInterface;
use douggonsouza\mvc\model\utils;

class model extends utils implements modelInterface
{   
    public    $table;
    public    $key;
    public    $dicionary = null;
    public    $options;
    protected $records;
    protected $error;
    protected $model = false;

    public function __construct(string $table = null, string $key = null)
    {
        $this->setTable($table);
        $this->setKey($key);
        $this->setModel(true);
    }

    /**
     * Informações das colunas visíveis
     *
     * array(
     *      'table'  => 'users',
     *      'key'    => 'user_id',
     *      'columns' => array(
     *              'user_id' => array(
     *              'label' => 'Id',
     *              'pk'    => true,
     *              'type'  => 'integer',
     *              'limit' => 11
     *          ),
     *      ),
     * );
     * @return void
     */
    public function visibleColumns()
    {
        return array();
    }

    /**
     * Arvore de validações por coluna
     *
     * @return array
     */
    public function validate()
    {
        return array();
    }

    /**
     * Exporta objeto do tipo dicionary
     * 
     * @param string $dicionarySQL
     * 
     * @return object
     */
    public function dicionary()
    {
        if(empty($this->getDicionary())){
            return null;
        }

        $resource = new resource();
        $dicionary = $resource->execute($this->getDicionary());
        if(!isset($dicionary)){
            $this->setError($resource->getError());
            return null;
        }
        return $dicionary;
    }

    /**
     * Exporta objeto do tipo dicionary
     * 
     * @param string $dicionarySQL
     * 
     * @return object
     */
    public function options()
    {
        if(empty($this->getDicionary())){
            return null;
        }

        $resource = new resource();
        $options = $resource->execute($this->getOptions());
        if(!isset($options)){
            $this->setError($resource->getError());
            return null;
        }
        return $options;
    }

    /**
     * Move o ponteiro para o prÃ³ximo
     * 
     */
    public function next()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->next();
    }

     /**
     * Move o ponteiro para o anterior
     * 
     */
    public function previous()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->previous();
    }

    /**
     * Move o ponteiro para o primeiro
     * 
     */
    public function first()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->first();
    }

    /**
     * Move o ponteiro para o Ãºltimo
     * 
     */
    public function last()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->last();
    }

    /**
     * Get the value of data
     */ 
    public function getData()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->getData();
    }

    /**
     * Get the value of data
     */ 
    public function getField(string $field)
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->getField($field);
    }

    /**
     * Preenche um campo com valor
     *
     * @param string $field
     * @param mixed $value
     * @return bool
     */
    public function setField(string $field, $value)
    {
        if(empty($this->getRecords())){
            $this->setRecords(new resource());
        }

        return $this->getRecords()->setField($field, $value);
    }

    /**
     * Get the value of isEof
     */ 
    public function isEof()
    {
        if(empty($this->getRecords())){
            return true;
        }

        return $this->getRecords()->getIsEof();
    }

    /**
     * Cardinalidade Muitos para um
     *
     * @param object $model
     * @param string $fieldDestine
     * @param string $fieldOrigen
     * @return void
     */
    public function manyForOne(object $model, string $fieldDestine, $fieldOrigem = null)
    {
        if(!isset($fieldOrigem)){
            $fieldOrigem = $fieldDestine;
        }

        if(!isset($model) && empty($model)){
            return null;
        }

        if(!isset($fieldDestine) && empty($fieldDestine)){
            return null;
        }

        $resource = new resource();
        $sql = sprintf("SELECT DISTINCT
                %3\$s.*
            FROM %3\$s
            JOIN %1\$s ON %1\$s.%2\$s = %3\$s.%4\$s AND %1\$s.active = 1
            WHERE
                %1\$s.%2\$s = %5\$s
                AND %3\$s.active = 1
            -- GROUP BY
            --     %3\$s.%4\$s
            ORDER BY
                %3\$s.%4\$s;",
            $this->getTable(),
            $fieldOrigem,
            $model->getTable(),
            $fieldDestine,
            $this->prepareValueByColumns(
                $this->type($this->infoColumns($model->getTable(),$fieldOrigem)[0]['Type']),
                $this->getField($fieldOrigem)
            )
        );

        if(!$resource->query($sql)){
            $this->setError($resource->getError());
            return null;
        }

        return $resource;
    }
    
    /**
     * Cardinalidade Muitos para Muitos
     *
     * @param object $model
     * @param string $fieldDestine
     * @param string $fieldOrigen
     * @return void
     */
    public function manyForMany(object $model, string $fieldDestine, string $fieldOrigem = null)
    {
        if(!isset($model) && empty($model)){
            return null;
        }

        if(!isset($fieldDestine) && empty($fieldDestine)){
            return null;
        }

        if(!isset($fieldOrigem)){
            $fieldOrigem = $fieldDestine;
        }

        $resource = new resource();
        $sql = sprintf("SELECT
                %3\$s.*
            FROM %1\$s
            JOIN %3\$s ON %3\$s.%4\$s = %1\$s.%2\$s
            ORDER BY
                %1\$s.%2\$s;",
            $this->getTable(),
            $fieldOrigem,
            $model->getTable(),
            $fieldDestine,
        );

        if(!$resource->query($sql)){
            return null;
        }

        return $resource;
    }

    /**
     * Popula o objeto data pelo array
     *
     * @param array $data
     * @return bool
     */
    public function populate(array $data)
    {
        if(empty($this->getTable())){
            $this->setError('Falta a definição da tabela.');
            return false;
        }

        if(empty($this->getRecords())){
            $this->setRecords(new resource());
        }

        // array do conteúdo
        $content = $this->dataByColumns($this->infoColumns($this->getTable()), $data);
        if(!$this->getRecords()->populate($content)){
            $this->setError('Erro na população do objeto Data.');
            return false;
        }

        return $this;
    }

    /**
     * Levanta as informações das columas para a tabela
     *
     * @param string $table
     * @return array
     */
    protected function infoColumns(string $table, $field = null)
    {
        if(!isset($table) || empty($table)){
            return null;
        }

        if(isset($field)){
            return $this->execute(
                sprintf(
                    "SHOW COLUMNS FROM %s WHERE Field='%s'",
                    $table,
                    $field
                )
            );
        }

        $sql = sprintf(
            'SHOW COLUMNS FROM %s',
            $table
        );
        return $this->execute($sql);
    }

    /**
     * Salva os dados do modelo
     *
     * @return bool
     */
    public function save()
    {
        if(empty($this->getRecords())){
            return false;
        }

        $resource = new resource();

        $this->validated($this->getData());

        $sql = $this->queryForSave($this->getData());
        if(empty($sql)){
            $this->setError('Erro na geração da query de salvamento.');
            return false;
        }

        if(!empty($this->getError())){
            return false;
        }

        if(!$this->beforeSave()){
            $this->setError('Erro na validação de pré salvamento.');
            return false;
        }

        if(!$resource->query($sql)){
            $this->setError($resource->getError());
            return false;
        }

        return true;
    }

    /**
     * Realiza validações antes do salvamento
     *
     * @return void
     */
    protected function beforeSave()
    {
        return true;
    }

    /**
     * Executa a validação dos campos
     * 
     * @param array $data
     * @return void
     */
    protected function validated($data)
    {
        if(!isset($data) || empty($data)){
            $this->setError('Não existem dados a serem salvos.');
        }

        $validate = $this->validate();
        if(empty($validate)){
            return;
        }

        foreach($data as $index => $item){
            foreach($validate[$index] as $valid){
                $valid->validate($item);
                if(!empty($valid->getError())){
                    $this->setError($valid->getError());
                }
            }
        }
    }

    /**
     * Expõe o total de linha afetadas pela query
     * @return int
    */
    public function total()
    {
        if(empty($this->getRecords())){
            return null;
        }

        return $this->getRecords()->total();
    }

    /**
     * Devolve array associativo de todos os registros
     * 
     * @return array|null
     */
    public function asArray()
    {
        if(empty($this->getRecords())){
            return null;
        }
        return $this->getRecords()->asArray();
    }

    /**
     * Executa uma instruÃ§Ã£o MySQL
     * 
     */
    public function query(string $sql)
    {
        if(empty($this->getRecords())){
            $this->setRecords(new resource());
        }

        return $this->getRecords()->query($sql);
    }

    /**
     * Salva os dados do modelo
     *
     * @return bool
     */
    public function delete()
    {
        if(empty($this->getRecords())){
            return false;
        }

        $resource = new resource();

        $sql = $this->queryForDelete($this->getData());
        if(empty($sql)){
            $this->setError('Erro na geração da query de deleção.');
            return false;
        }

        if(!$this->beforeDelete()){
            $this->setError('Erro na validação antes da deleção.');
            return false;
        }

        if(!$resource->query($sql)){
            $this->setError($resource->getError());
            return false;
        }

        return true;
    }

    /**
     * Validação antes da deleção
     *
     * @return void
     */
    protected function beforeDelete()
    {
        return true;
    }

    /**
     * Executa uma instrução MySQL
     * 
     */
    public function execute(string $sql)
    {
        if(empty($this->getRecords())){
            $this->setRecords(new resource());
        }

        return $this->getRecords()->execute($sql);
    }

    /**
     * Carrega a propriedade records com um resource
     *
     * @return void
     */
    public function records(string $sql = null)
    {
        $this->records = new resource();
        if(isset($sql)){
            $this->records->query($sql);
            return true;
        }
        $this->records->query("SELECT * FROM ".$this->getTable().";");
        return true;
    }

    /**
     * Busca entre os registros da tabela
     *
     * @param array $search
     * @return void
     */
    public function seek(array $search = null)
    {
        $this->setRecords(new resource());
        if(!$this->getRecords()->seek($this->sqlSeek($search))){
            $this->setError($this->getRecords()->getError());
            return null;
        }

        return $this;
    }

    /**
     * Busca entre os registros
     *
     * @param string $table
     * @return bool
     */
    public function search(array $search)
    {
        if(empty($this->getTable())){
            return null;
        }

        if(!isset($search) || empty($search)){
            return null;
        }

        $content = $this->filterByColumns($search);
        array_walk ($content, function(&$item, $key){
            $item = $key.' = '.$item;
        });

        $this->setRecords(new resource());
        if(!$this->getRecords()->search(
            $this->getTable(),
            $content
        )){
            $this->setError($this->getRecords()->getError());
            return null;
        }

        $this->setModel(true);

        return $this;
    }

    public function isNew()
    {
        if(empty($this->getRecords())){
            return null;
        }
        return $this->getRecords()->getNew();
    }

    /**
     * Devolve sql para a realização da busca
     *
     * @param array $where
     * @return string
     */
    public function sqlSeek(array $where = null)
    {
        if(empty($this->getTable())){
            return null;
        }

        if(!isset($where)){
            $where = array( $this->getTable().'.active = 1');
        }

        return sprintf(
            'SELECT * FROM %1$s WHERE %2$s;',
            $this->getTable(),
            implode(' AND ', $where)
        );
    }

    /**
     * Cria query de Save
     *
     * @param array $infoColumns
     * @param array $data
     * @return string
     */
    public function queryForSave(array $data)
    {
        if(!isset($data) || empty($data)){
            $this->setError('Não é permitido parâmetro data nulo.');
            return false;
        }

        $infoColumns = $this->infoColumns($this->getTable());
        if(!isset($infoColumns) || empty($infoColumns)){
            $this->setError('Não é permitido parâmetro infoColumns nulo.');
            return false;
        }

        $where = null;

        // array do conteúdo

        $content = array();
        foreach($infoColumns as $item){
            if($item['Key'] == 'PRI'){
                if(isset($data[$item['Field']])){
                    $where = $item['Field'].' = '.$this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    );
                }
                continue;
            }

            if(isset($where)){
                $content[$item['Field']] = $item['Field'].' = '.$this->prepareValueByColumns(
                    $this->type($item['Type']),
                    $data[$item['Field']]
                ).'';
                continue;
            }
            $content[$item['Field']] = $this->prepareValueByColumns(
                $this->type($item['Type']),
                $data[$item['Field']]
            );
            // created
            if(isset($item['Field']) && $item['Field'] == 'created'){
                $content[$item['Field']] = 'NOW()';
            }
            // modified
            if(isset($item['Field']) && $item['Field'] == 'modified'){
                $content[$item['Field']] = 'NOW()';
            }
        }

        // update
        if(isset($where)){
            $sql = sprintf(
                "UPDATE %1\$s SET %2\$s WHERE %3\$s;",
                $this->getTable(),
                implode(', ',$content),
                $where
            );
            return $sql;
        }
        // save
        $sql = sprintf(
            "INSERT INTO %1\$s (%2\$s) VALUES (%3\$s);",
            $this->getTable(),
            implode(', ', array_keys($content)),
            implode(', ',$content),
        );
        return $sql;
    }

    /**
     * Cria query de Save
     *
     * @param array $data
     * @return string
     */
    public function queryForDelete(array $data)
    {
        if(!isset($data) || empty($data)){
            $this->setError('Nâo é permitido parâmetro data nulo.');
            return false;
        }

        $infoColumns = $this->infoColumns($this->getTable());
        if(!isset($infoColumns) || empty($infoColumns)){
            $this->setError('Não é permitido parâmetro infoColumns nulo.');
            return false;
        }

        // existe id
        $where = null;
        $infoKey = $this->infoColumns($this->getTable(),$this->getKey());
        if(isset($data[$infoKey['Field']])){
            $where = $infoKey['Field'].' = '.$this->prepareValueByColumns(
                $this->type($infoKey['Type']),
                $data[$infoKey['Field']]
            );
        }
        if(!isset($where)){
            $this->setError('Não é possível deletar um novo resource.');
            return false;
        }

        // update
        $sql = sprintf(
            "DELETE FROM %1\$s WHERE %2\$s;",
            $this->getTable(),
            $where
        );
        return $sql;
    }

    protected function dataByColumns(array $infoColumns, array $data)
    {
        $content = array();

        if(!isset($infoColumns) || empty($infoColumns) || !isset($data) || empty($data)){
            return $content;
        }

        // array do conteúdo
        foreach($infoColumns as $item){
            if(isset($data[$item['Field']])){
                $content[$item['Field']] = trim($data[$item['Field']]);
            }
        }

        return $content;
    }

    protected function filterByColumns(array $data)
    {
        $content = array();
        $infoColumns = $this->infoColumns($this->getTable());
        if(!isset($infoColumns) || empty($infoColumns) || !isset($data) || empty($data)){
            return $content;
        }

        // array do conteúdo
        foreach($infoColumns as $item){
            if(isset($data[$item['Field']])){
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
     * é um modelo sim ou não
     *
     * @return  self
     */ 
    public function isModel()
    {
        return $this->model;
    }

    /**
     * Colhe o valor para table
     */ 
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Define o valor para table
     *
     * @param string $table
     *
     * @return  self
     */ 
    public function setTable($table)
    {
        if(isset($table) && !empty($table)){
            $this->table = $table;
        }
    }

    /**
     * Colhe o valor para key
     */ 
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Define o valor para key
     *
     * @param string $key
     *
     * @return  self
     */ 
    public function setKey($key)
    {
        if(isset($key) && !empty($key)){
            $this->key = $key;
        }
    }

    /**
     * Get the value of records
     */ 
    public function getRecords()
    {
        return $this->records;
    }

    protected function setRecords($records)
    {
        if(isset($records) && !empty($records)){
            $this->records = $records;
        }
    }

    /**
     * Get the value of dicionary
     */ 
    public function getDicionary()
    {
        return $this->dicionary;
    }

    /**
     * Set the value of dicionary
     *
     * @return  self
     */ 
    protected function setDicionary($dicionary)
    {
        if(isset($dicionary) && !empty($dicionary)){
            $this->dicionary = $dicionary;
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
     * Get the value of model
     */ 
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the value of model
     *
     * @return  self
     */ 
    protected function setModel($model)
    {
        if(isset($model)){
            $this->model = $model;
        }
        return $this;
    }

    /**
     * Get the value of options
     */ 
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value of options
     *
     * @return  self
     */ 
    private function setOptions($options)
    {
        if(isset($options) && !empty($options)){
            $this->options = $options;
        }

        return $this;
    }
}
