<?php

namespace douggonsouza\mvc\model\connection;

use douggonsouza\mvc\model\connection\connInterface;
use douggonsouza\mvc\model\resource\records;
use douggonsouza\mvc\model\resource\recordsInterface;


abstract class conn implements connInterface
{
	protected static $host;
	protected static $login;
	protected static $password;
	protected static $schema;

	public static $error;

	private static $connection  = null;

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
	public static function connection(string $host, string $login, string $password, string $schema)
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
     * Method select
     *
     * @param string $sql [explicite description]
     *
     * @return recordsInterface
     */
    public static function select(string $sql)
    {
        if(!isset($sql) || empty($sql) || !self::getConnection()){
            return false;
        }
        
        try{
            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');

            $resource = mysqli_query(self::getConnection(), (string) $sql);
            
            if(!empty(mysqli_error(self::getConnection()))){
                self::setError(mysqli_error(self::getConnection()));
                return false;
            }

            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');

			if(is_bool($resource)){
				self::setError('Não trata-se de um recurso.');
				return null;
			}

			$records = new records($resource);
			$records->data();

            return $records;
        }
        catch(\Exception $e){
			self::setError( $e->getMessage());
            return false;
        }
    }
  
    /**
     * Method selectWithArray
     *
     * @param string $sql [explicite description]
     *
     * @return void
     */
    public static function selectAsArray(string $sql)
    {
        if(!isset($sql) || empty($sql) || !self::getConnection()){
            return false;
        }
        
        try{
            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');

            $resource = mysqli_query(self::getConnection(), (string) $sql);
            
            if(!empty(mysqli_error(self::getConnection()))){
                self::setError(mysqli_error(self::getConnection()));
                return false;
            }

            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');

			if(is_bool($resource)){
				self::setError('Não trata-se de um recurso.');
				return null;
			}

            return mysqli_fetch_all($resource, MYSQLI_ASSOC);
        }
        catch(\Exception $e){
			self::setError( $e->getMessage());
            return false;
        }
    }
    
    /**
     * Method query
     *
     * @param string $sql [explicite description]
     *
     * @return void
     */
    public static function query(string $sql)
    {
        if(!isset($sql) || empty($sql) || !self::getConnection()){
            return false;
        }
        
        try{
            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');

            $resource = mysqli_query(self::getConnection(), (string) $sql);
            
            if(!empty(mysqli_error(self::getConnection()))){
                self::setError(mysqli_error(self::getConnection()));
                return false;
            }

            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');

			if(!is_bool($resource)){
				return null;
			}

            return $resource;
        }
        catch(\Exception $e){
			self::setError( $e->getMessage());
            return false;
        }
    }

	/**
     * Inicia transação
     * 
     * @return boolean
     */
    static public function begin()
    {

		// inicia sessão de transação
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');
        mysqli_begin_transaction(self::getConnection());
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');
		
        return true;
	}

    /**
     * Faz commit na transação iniciada
     * @return boolean
     */
    static public function commit()
    {
		// confirma sessão de transação
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');
        mysqli_commit(self::getConnection());
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');
		
        return true;
    }

    /**
     * Faz rollback na transação iniciada
     * @return boolean
     */
    static public function rollback()
    {
		// desfaz sessão de transação
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');
        mysqli_rollback(self::getConnection());
		mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');
		
        return true;
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
	public static function getError()
	{
		return self::$error;
	}

	/**
	 * Set the value of error
	 *
	 * @return  self
	 */ 
	public static function setError(string $error)
	{
		if(isset($error) && !empty($error)){
			self::$error = $error;
		}
	}
}
