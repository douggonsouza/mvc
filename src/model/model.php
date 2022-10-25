<?php

namespace douggonsouza\mvc\model;

use douggonsouza\mvc\model\resource\records;
use douggonsouza\mvc\model\connection\conn;
use douggonsouza\mvc\model\resource\recordsInterface;
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
    protected $new = true;
    
    /**
     * Method __construct
     *
     * @param int $id [explicite description]
     * @param string $table [explicite description]
     * @param string $key [explicite description]
     *
     * @return void
     */
    public function __construct(int $id = null, string $table = null, string $key = null)
    {
        $this->setTable($table);
        $this->setKey($key);
        if(isset($id) && !empty($id)){
            $this->search(array(
                $this->getKey() => $id
            ));
            $this->setModel(true);
            $this->setNew(false);
        }
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
     * Method dicionary - Exporta objeto do tipo dicionary
     *
     * @return array
     */
    public function dicionary()
    {
        if(empty($this->getDicionary())){
            return null;
        }

        return $this->queryUncoupled($this->getDicionary());
    }
   
    /**
     * Method options - Exporta objeto do tipo dicionary
     *
     * @param array $where [explicite description]
     *
     * @return array
     */
    public function options(array $where = null)
    {
        // define condições
        $lWhere = '';
        if(isset($where) && !empty($where)){
            $lWhere = sprintf(
                'WHERE %s',
                implode(' AND ', $where)
            );
        }

        // forma query
        $sql = sprintf(
            $this->getOptions(),
            $lWhere
        );

        return $this->queryUncoupled($sql);
    }
    
    /**
     * Method next - Move o ponteiro para o próximo
     *
     * @return bool
     */
    public function next()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->next();
    }

    /**
     * Method previous - Move o ponteiro para o anterior
     *
     * @return bool
     */
    public function previous()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->previous();
    }
   
    /**
     * Method first - Move o ponteiro para o primeiro
     *
     * @return void
     */
    public function first()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->first();
    }
   
    /**
     * Method last - Move o ponteiro para o último
     *
     * @return bool
     */
    public function last()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->last();
    }
     
    /**
     * Method getData - Colhe o array Data
     *
     * @return array
     */
    public function getData()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->getData();
    }
    
    /**
     * Method getField - Colhe array com os campos
     *
     * @param string $field [explicite description]
     *
     * @return array
     */
    public function getField(string $field)
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->getField($field);
    }

    /**
     * Method setField - Preenche um campo com valor
     *
     * @param string $field [explicite description]
     * @param $value $value [explicite description]
     *
     * @return void
     */
    public function setField(string $field, $value)
    {
        if(empty($this->getRecords())){
            $this->setRecords(new records());
        }

        return $this->getRecords()->setField($field, $value);
    }
   
    /**
     * Method value - Colhe ou Seta o valor do campo
     *
     * @param string $field [explicite description]
     * @param $value $value [explicite description]
     *
     * @return mixed
     */
    public function value(string $field, $value = null)
    {
        if(empty($this->getRecords())){
            return false;
        }

        if(!isset($field) || empty($field)){
            return false;
        }

        if(isset($value)){
            return $this->getRecords()->setField($field, $value);
        }

        return $this->getRecords()->getField($field);
    }
    
    /**
     * Method isEof - Fim dos registros
     *
     * @return void
     */
    public function isEof()
    {
        if(empty($this->getRecords())){
            return true;
        }

        return $this->getRecords()->getIsEof();
    }
   
    /**
     * Method relationship - Colhe os dados do relacionamento
     *
     * @param modelInterface $model [explicite description]
     * @param bool $resource [explicite description]
     *
     * @return mixed
     */
    public function relationship(modelInterface $model, bool $resource = false)
    {
        if(empty($this->getRecords())){
            return null;
        }

        $sql = sprintf(
            "SELECT t1.*
            FROM %1\$s t1 
            JOIN %3\$s t2 ON t2.%2\$s = t1.%2\$s AND t2.active = 'yes'
            WHERE t2.active = 'yes' AND t2.%4\$s = %5\$d;",
            $model->getTable(),             // %1\$s
            $model->getKey(),               // %2\$s
            $this->getTable(),              // %3\$s
            $this->getKey(),                // %4\$s
            $this->value($this->getKey())   // %5\$d
        );

        if($resource){
            return conn::select($sql);
        }

        return conn::selectAsArray($sql);
        
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
            $this->setRecords(new records());
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
     * Method infoColumns - Colhe as informações para as colunas da tabela
     *
     * @param string $table [explicite description]
     * @param $field $field [explicite description]
     *
     * @return void
     */
    protected function infoColumns(string $table, $field = null)
    {
        if(!isset($table) || empty($table)){
            return null;
        }

        if(isset($field)){
            return $this->queryUncoupled(sprintf(
                "SHOW COLUMNS FROM %s WHERE Field='%s'",
                $table,
                $field
            ));
        }

        return $this->queryUncoupled(sprintf(
            'SHOW COLUMNS FROM %s',
            $table
        ));
    }
 
    /**
     * Method save - Salva os dados do modelo
     *
     * @return bool
     */
    public function save()
    {
        if(empty($this->getRecords())){
            return false;
        }

        $records = new records();

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

        if(!$records->query($sql)){
            $this->setError($records->getError());
            return false;
        }

        return true;
    }
   
    /**
     * Method beforeSave - Processamento antes de salvar
     *
     * @return bool
     */
    protected function beforeSave()
    {
        return true;
    }
   
    /**
     * Method validated - Executa a validação dos campos
     *
     * @param $data $data [explicite description]
     *
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
     * Method total - Expõe o total de linha afetadas pela query
     *
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
     * Method exist - O modelo existe
     *
     * @return void
     */
    public function exist()
    {
        if(empty($this->getRecords())){
            return false;
        }

        return $this->getRecords()->exist();
    }
   
    /**
     * Method asAllArray
     *
     * @return void
     */
    public function asAllArray()
    {
        if(empty($this->getRecords())){
            return null;
        }

        if(!$this->getRecords()->first()){
            return null;
        }

        return $this->getRecords()->asAllArray();
    }
    
    /**
     * Method delete - Deleta o registro
     *
     * @return bool
     */
    public function delete()
    {
        if(empty($this->getRecords())){
            return false;
        }

        $records = new records();

        $sql = $this->queryForDelete($this->getData());
        if(empty($sql)){
            $this->setError('Erro na geração da query de deleção.');
            return false;
        }

        if(!$this->beforeDelete()){
            $this->setError('Erro na validação antes da deleção.');
            return false;
        }

        if(!$records->query($sql)){
            $this->setError($records->getError());
            return false;
        }

        return true;
    }
    
    /**
     * Method beforeDelete - Antes da deleção
     *
     * @return void
     */
    protected function beforeDelete()
    {
        return true;
    }
    
    /**
     * Method queryUncoupled - Executa uma instrução MySQL desaclopada
     *
     * @param string $sql [explicite description]
     *
     * @return void
     */
    public function queryUncoupled(string $sql)
    {
        $records = conn::selectAsArray($sql);
        if(empty($records)){
            return null;
        }

        return $records;
    }

    /**
     * Busca entre os registros da tabela
     *
     * @param array $search
     * @return self
     */
    // public function seek(array $search = null)
    // {
    //     $this->setRecords(new records());
    //     if(!$this->getRecords()->seek($this->sqlSeek($search))){
    //         $this->setError($this->getRecords()->getError());
    //         return null;
    //     }

    //     return $this;
    // }
    
    /**
     * Method search - Busca entre os registros do banco
     *
     * @param array $search [explicite description]
     *
     * @return self
     */
    public function search(array $search)
    {
        if(!isset($this->table) || empty($this->table)){
            $this->setError('Não encontrado o nome da tabela.');
            return false;
        }

        if(!isset($search) || empty($search)){
            $this->setError('Não é permitido parâmetro nulo.');
            return false;
        }

        $content = $this->filterByColumns($search);
        array_walk ($content, function(&$item, $key){
            $item = $key.' = '.$item;
        });

        $this->setRecords(conn::select(sprintf(
            "SELECT * FROM %1\$s WHERE %2\$s;",
            $this->getTable(),
            implode(' AND ', $content)
        )));
        
        // if(!$this->getResource()){
        //     $this->setError(self::getConn()->error);
        //     return false;
        // }

        return $this;
    }
   
    /**
     * Method isNew - É novo
     *
     * @return bool
     */
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

            // active
            if(isset($item['Field']) && $item['Field'] == 'active'){
                if(isset($data[$item['Field']])){
                    $content[$item['Field']] = $item['Field'].' = '.$this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ).'';
                }
                continue;
            }
            // created
            if(isset($item['Field']) && $item['Field'] == 'created'){
                $content[$item['Field']] = 'NOW()';
                if(isset($data[$item['Field']])){
                    $content[$item['Field']] = $item['Field'].' = '.$this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ).'';
                }
                continue;
            }
            // modified
            if(isset($item['Field']) && $item['Field'] == 'modified'){
                $content[$item['Field']] = 'NOW()';
                if(isset($data[$item['Field']])){
                    $content[$item['Field']] = $item['Field'].' = '.$this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ).'';
                }
                continue;
            }
            // removed
            if(isset($item['Field']) && $item['Field'] == 'removed'){
                if(isset($data[$item['Field']])){
                    $content[$item['Field']] = $item['Field'].' = '.$this->prepareValueByColumns(
                        $this->type($item['Type']),
                        $data[$item['Field']]
                    ).'';
                }
                continue;
            }

            $content[$item['Field']] = $this->prepareValueByColumns(
                $this->type($item['Type']),
                $data[$item['Field']]
            );
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
            $this->setError('Não é possível deletar um novo records.');
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
    
    /**
     * Method setRecords
     *
     * @param recordsInterface $records [explicite description]
     *
     * @return void
     */
    protected function setRecords(recordsInterface $records)
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

    /**
     * Get the value of new
     */ 
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Set the value of new
     *
     * @return  self
     */ 
    public function setNew($new)
    {
        if(isset($new) && !empty($new)){
            $this->new = $new;
        }

        return $this;
    }
}
