<?php

namespace douggonsouza\mvc\model;

use douggonsouza\mvc\model\connection\conn;
use douggonsouza\mvc\model\tableInterface;
use douggonsouza\mvc\model\helps;
use douggonsouza\mvc\model\resource\source;
use douggonsouza\mvc\model\resource\sourceInterface;
use douggonsouza\propertys\propertys;
use douggonsouza\propertys\propertysInterface;

/**
 * table
 * 
 * Gerênciamento da model
 * 
 * @vesion 1.0.0
 * 2023-04-02 
 */
class table extends helps implements tableInterface
{
    const     SQL_MODEL = "SELECT * FROM %1\$s WHERE active = 'yes' AND %2\$s;";

    public    $table;
    public    $key;
    public    $dicionary = null;
    public    $options;
    protected $data;
    protected $index = -1;
    protected $source;
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
    public function __construct($id = null)
    {
        if (isset($id) && !empty($id)) {

            // for int
            if (is_int($id)) {
                $this->search(array(
                    $this->getKey() => $id
                ));
            }

            // for array
            if (is_array($id)) {
                $this->search($id);
            }
        }
    }
    
    /**
     * Method isModel - é um modelo true ou false
     *
     * @return void
     */
    public function isModel()
    {
        return $this->getModel();
    }
    
    /**
     * Method __destruct
     *
     * @return void
     */
    function __destruct()
    {
        if (empty($this->getSource())) {
            return;
        }

        $this->getSource()->close();
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
        if (isset($where) && !empty($where)) {
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

        return (new source(conn::select($sql)))->allArray();
    }

    /**
     * Method next - Move o ponteiro para o próximo
     *
     * @return bool
     */
    public function next()
    {
        if (empty($this->getSource())) {
            throw new \Exception("Não existe recurso.");
        }

        $this->setIndex($this->getIndex() + 1);
        $this->getSource()->dataSeek($this->getIndex());
        $this->data((array) $this->getSource()->fetchObject());

        return true;
    }

    /**
     * Method previous - Move o ponteiro para o anterior
     *
     * @return bool
     */
    public function previous()
    {
        if (empty($this->getSource())) {
            throw new \Exception("Não existe recurso.");
        }

        $this->setIndex($this->getIndex() - 1);
        $this->getSource()->dataSeek($this->getIndex());
        $this->data((array) $this->getSource()->fetchObject());

        return true;
    }

    /**
     * Method first - Move o ponteiro para o primeiro
     *
     * @return void
     */
    public function first()
    {
        if (empty($this->getSource())) {
            throw new \Exception("Não existe recurso.");
        }

        $this->setIndex(0);
        $this->getSource()->dataSeek($this->getIndex());
        $this->data((array) $this->getSource()->fetchObject());

        return true;
    }

    /**
     * Method last - Move o ponteiro para o último
     *
     * @return bool
     */
    public function last()
    {
        if (empty($this->getSource())) {
            throw new \Exception("Não existe recurso.");
        }

        $this->setIndex($this->getSource()->total() - 1);
        $this->getSource()->dataSeek($this->getIndex());
        $this->data((array) $this->getSource()->fetchObject());

        return true;
    }

    /**
     * Atualiza a propriedade data
     *
     * @return bool
     */
    private function data(array $source)
    {
        if(empty($this->getData())){
            $this->setData(new propertys($source));
            return true;
        }

        $this->getData()->add($source);
        return true;
    }
    
    /**
     * Method get
     *
     * @param string $name [explicite description]
     *
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->getData()->$name;
    }

    /**
     * Method set
     *
     * @param string $name [explicite description]
     * @param $value $value [explicite description]
     *
     * @return bool
     */
    public function set(string $name, $value)
    {
        $this->getData()->add(array($name => $value));

        return true;
    }

    /**
     * Method relationship - Colhe os dados do relacionamento
     *
     * @param modelInterface $model [explicite description]
     * @param bool $resource [explicite description]
     *
     * @return mixed
     */
    // public function relationship(modelInterface $model, bool $resource = false)
    // {
    //     if (empty($this->getSource())) {
    //         throw new \Exception("Não existe recurso.");
    //     }

    //     $sql = sprintf(
    //         "SELECT t1.*
    //         FROM %1\$s t1 
    //         JOIN %3\$s t2 ON t2.%2\$s = t1.%2\$s AND t2.active = 'yes'
    //         WHERE t2.active = 'yes' AND t2.%4\$s = %5\$d;",
    //         $model->getTable(),             // %1\$s
    //         $model->getKey(),               // %2\$s
    //         $this->getTable(),              // %3\$s
    //         $this->getKey(),                // %4\$s
    //         $this->value($this->getKey())   // %5\$d
    //     );

    //     if ($resource) {
    //         return conn::select($sql);
    //     }

    //     return conn::selectAsArray($sql);
    // }

    /**
     * Popula o objeto data pelo array
     *
     * @param array $data
     * @return bool
     */
    public function populate(array $data)
    {
        if (empty($this->getTable())) {
            $this->setError('Falta a definição da tabela.');
            return false;
        }

        if (empty($this->getData())) {
            $this->setData(new propertys());
        }

        // array do conteúdo
        $content = $this->dataByColumns($this->infoColumns($this->getTable()), $data);
        if (!$this->getData()->add($content)) {
            $this->setError('Erro na população do objeto Data.');
            return false;
        }

        return $this;
    }

    /**
     * Method save - Salva os dados do modelo
     *
     * @return bool
     */
    public function save()
    {
        // $this->validated($this->getData());

        $sql = $this->queryForSave((array) $this->getData());
        if (empty($sql)) {
            $this->setError('Erro na geração da query de salvamento.');
            return false;
        }

        $id = conn::query($sql);
        if (is_bool($id) && !$id) {
            $this->setError(conn::getError());
            return false;
        }

        $this->search(array($this->key => $id));

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
        if (!isset($data) || empty($data)) {
            $this->setError('Não existem dados a serem salvos.');
        }

        $validate = $this->validate();
        if (empty($validate)) {
            return;
        }

        foreach ($data as $index => $item) {
            foreach ($validate[$index] as $valid) {
                $valid->validate($item);
                if (!empty($valid->getError())) {
                    $this->setError($valid->getError());
                }
            }
        }
    }

    /**
     * Method delete - Deleta o registro
     *
     * @return bool
     */
    public function delete()
    {
        if (empty($this->getSource())) {
            return false;
        }

        $sql = $this->queryForDelete((array) $this->getData());
        if (empty($sql)) {
            $this->setError('Erro na geração da query de deleção.');
            return false;
        }

        if (!conn::query($sql)) {
            $this->setError(conn::getError());
            return false;
        }

        return true;
    }

    /**
     * Method softDelete - Inativa o registro
     *
     * @return bool
     */
    public function softDelete(int $removed = 1)
    {
        if (empty($this->getSource())) {
            return false;
        }

        $sql = $this->queryForSoftDelete((array) $this->getData(), $removed);
        if (empty($sql)) {
            $this->setError('Erro na geração da query de deleção.');
            return false;
        }

        if (!conn::query($sql)) {
            $this->setError(conn::getError());
            return false;
        }

        return true;
    }

    /**
     * Method search - Busca entre os registros do banco
     *
     * @param array $search [explicite description]
     *
     * @return self
     */
    public function search(array $search)
    {
        if (!isset($this->table) || empty($this->table)) {
            $this->setError('Não encontrado o nome da tabela.');
            return false;
        }

        if (!isset($search) || empty($search)) {
            $this->setError('Não é permitido parâmetro nulo.');
            return false;
        }

        $content = $this->prepareArrayWhere($this->getTable(), $search);
        if (!isset($content) || empty($content)) {
            return false;
        }

        $sql = sprintf(
            self::SQL_MODEL,
            $this->getTable(),
            implode(' AND ', $content)
        );

        $this->setSource(new source(conn::select($sql)));
        if (!empty(conn::getError())) {
            $this->setError(conn::getError());
            return false;
        }

        if($this->getSource()->total() > 0){
            $this->setModel(true);
            $this->setNew(false);
            $this->first();
        }
        
        return $this;
    }

    /**
     * Method isNew - É novo
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->getNew();
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
        if (!isset($data) || empty($data)) {
            $this->setError('Não é permitido parâmetro data nulo.');
            return false;
        }

        $infoColumns = $this->infoColumns($this->getTable());
        if (!isset($infoColumns) || empty($infoColumns)) {
            $this->setError('Não é permitido parâmetro infoColumns nulo.');
            return false;
        }

        $where = null;

        // array do conteúdo

        $content = array();
        foreach ($infoColumns as $item) {
            if ($item['Key'] == 'PRI') {
                if (isset($data[$item['Field']])) {
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
                $this->getTable(),
                implode(', ', $content),
                $where
            );
            return $sql;
        }
        // save
        $sql = sprintf(
            "INSERT INTO %1\$s (%2\$s) VALUES (%3\$s);",
            $this->getTable(),
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
    public function queryForDelete(array $data)
    {
        if (!isset($data) || empty($data)) {
            $this->setError('Nâo é permitido parâmetro data nulo.');
            return false;
        }

        $infoColumns = $this->infoColumns($this->getTable());
        if (!isset($infoColumns) || empty($infoColumns)) {
            $this->setError('Não é permitido parâmetro infoColumns nulo.');
            return false;
        }

        // existe id
        $where = null;
        $infoKey = $this->infoColumns($this->getTable(), $this->getKey());
        if (isset($data[$infoKey['Field']])) {
            $where = $infoKey['Field'] . ' = ' . $this->prepareValueByColumns(
                $this->type($infoKey['Type']),
                $data[$infoKey['Field']]
            );
        }
        if (!isset($where)) {
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

    /**
     * Method queryForSoftDelete - Cria query de inativação
     *
     * @param array $data
     * @param int $removed
     *
     * @return string
     */
    public function queryForSoftDelete(array $data, int $removed = 1)
    {
        if (!isset($data) || empty($data)) {
            $this->setError('Nâo é permitido parâmetro data nulo.');
            return false;
        }

        // existe id
        $where = null;
        $infoKey = $this->infoColumns($this->getTable(), $this->getKey());
        if (isset($data[$infoKey['Field']])) {
            $where = $infoKey['Field'] . ' = ' . $this->prepareValueByColumns(
                $this->type($infoKey['Type']),
                $data[$infoKey['Field']]
            );
        }
        if (!isset($where)) {
            $this->setError('Não é possível deletar um novo records.');
            return false;
        }

        // update
        $sql = sprintf(
            "UPDATE FROM %1\$s SET active='no',removed=%2\$d WHERE %3\$s;",
            $this->getTable(),
            $removed,
            $where
        );
        return $sql;
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
    protected function setTable($table)
    {
        if (isset($table) && !empty($table)) {
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
    protected function setKey($key)
    {
        if (isset($key) && !empty($key)) {
            $this->key = $key;
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
        if (isset($dicionary) && !empty($dicionary)) {
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
    protected function setError($error)
    {
        if (isset($error) && !empty($error)) {
            if (!is_array($this->getError())) {
                $this->error = array();
            }

            if (is_array($error)) {
                $this->error = array_merge($this->error, $error);
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
        if (isset($model)) {
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
    protected function setOptions($options)
    {
        if (isset($options) && !empty($options)) {
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
    protected function setNew($new)
    {
        if (isset($new)) {
            $this->new = $new;
        }

        return $this;
    }

    /**
     * Get the value of source
     * 
     * @return sourceInterface
     */ 
    protected function getSource()
    {
        return $this->source;
    }

    /**
     * Set the value of source
     *
     * @return  self
     */ 
    protected function setSource(sourceInterface $source)
    {
        if(isset($source) && !empty($source)){
            if(!empty($this->source)){
                $this->getSource()->close();
            }
            $this->source = $source;
        }

        return $this;
    }


    /**
     * Get the value of data
     */ 
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */ 
    private function setData(propertysInterface $data)
    {
        if(isset($data) && !empty($data)){
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Get the value of index
     */ 
    protected function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the value of index
     *
     * @return  self
     */ 
    private function setIndex($index)
    {
        if(isset($index)){
            $this->index = $index;
        }

        return $this;
    }
}
