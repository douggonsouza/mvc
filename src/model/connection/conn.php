<?php

namespace douggonsouza\mvc\model\connection;

abstract class conn
{
	protected static $host;
	protected static $login;
	protected static $password;
	protected static $schema;

	public $error = array();

	static private $connection  = null;
	static public $transaction;

	private function __construct(){	}
	
	/**
	 * Conecta com o banco de dados
	 *
	 * @param string $host
	 * @param string $login
	 * @param string $password
	 * @param string $schema
	 * @return void
	 */
	static public function connection(string $host, string $login, string $password, string $schema)
	{
		self::$host  = $host;
		self::$login = $login;
		self::$password = $password;
		self::$schema = $schema;

        if(!isset(self::$connection)){
			self::$connection = mysqli_connect(
				self::getHost(),
				self::getLogin(),
				self::getPassword(),
				self::getSchema()
			);
			if (mysqli_connect_errno()) {
				exit(sprintf("Connect failed: %s\n", mysqli_connect_error()));
			}
		}
		return self::$connection;
    }
    	
	/**
	 * Evento destruidor da classe
	 */
	function __destruct()
	{
		self::$connection = null;	
	}

	/**
	 * Get the value of conn
	 */ 
	static public function getConnection()
	{
		if(!isset(self::$connection)){
			if(!isset(self::$host) || !isset(self::$login) || !isset(self::$password) || !isset(self::$schema)){
				self::setError("Não existem dados de conexão");
				return null;
			}

			self::connection(
                self::getHost(),
                self::getLogin(),
                self::getPassword(),
                self::getSchema(),
            );
            if(!isset(self::$connection)){
                self::setError('Erro durante a conexão.');
                return null;
            }
		}

		return self::$connection;
	}

    /**
     * Inicia transação
     * 
     * @return boolean
     */
    static public function beginTransaction()
    {

		// inicia sessão de transação
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');
        self::setTransaction(mysqli_query (self::getConnection(), 'START TRANSACTION'));
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');
		
        return true;
	}
	
    /**
     * Faz commit na transação iniciada
     * @return boolean
     */
    static public function commitTransaction()
    {
		// confirma sessão de transação
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');
        self::setTransaction(mysqli_query (self::getConnection(), 'COMMIT'));
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');
		
        return true;
    }

    /**
     * Faz rollback na transação iniciada
     * @return boolean
     */
    static public function rollbackTransaction()
    {
		// desfaz sessão de transação
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');
        self::setTransaction(mysqli_query (self::getConnection(), 'ROLLBACK'));
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');
		
        return true;
    }


	/**
	 * Get the value of transaction
	 */ 
	static public function getTransaction()
	{
		return self::$transaction;
	}

	/**
	 * Set the value of transaction
	 *
	 * @return  self
	 */ 
	static public function setTransaction($transaction)
	{
		if(isset($transaction) && !empty($transaction))
			self::$transaction = $transaction;
	}

	/**
	 * Get the value of host
	 */ 
	public static function getHost()
	{
		return self::$host;
	}

	/**
	 * Get the value of login
	 */ 
	public static function getLogin()
	{
		return self::$login;
	}

	/**
	 * Get the value of password
	 */ 
	public static function getPassword()
	{
		return self::$password;
	}

	/**
	 * Get the value of schema
	 */ 
	public static function getSchema()
	{
		return self::$schema;
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
			$this->error[] = $error;
		}

		return $this;
	}
}
