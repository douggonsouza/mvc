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

	private static $connection = null;

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
		self::$host     = $host;
		self::$login    = $login;
		self::$password = $password;
		self::$schema   = $schema;

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
    }

	/**
	 * Get the value of conn
	 */ 
	public static function getConnection()
	{
		if(!isset(self::$connection)){
			self::setError("Não existem dados de conexão");
			return null;
		}

		return self::$connection;
	}
    
    /**
     * Method select
     *
     * @param string $sql [explicite description]
     *
     * @return object
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

            return $resource;
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
		$result = false;

        if(!isset($sql) || empty($sql) || !self::getConnection()){
            return false;
        }
        
        try{
            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 0;');

            $result = mysqli_query(self::getConnection(), (string) $sql);

            if(!empty(mysqli_error(self::getConnection()))){
                self::setError(mysqli_error(self::getConnection()));
                return false;
            }
				
			if(!is_bool($result)){
				mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');
				return false;
			}

			$resource = mysqli_query(self::getConnection(), 'Select LAST_INSERT_ID() FROM DUAL;');
            mysqli_query(self::getConnection(), 'SET SQL_SAFE_UPDATES = 1;');

			$insertId = (mysqli_fetch_all($resource))[0][0];
			if(!isset($insertId) || empty($insertId)){
				return true;
			}

			$resource->close();
            return $insertId;
        }
        catch(\Exception $e){
			self::setError( $e->getMessage());
            return false;
        }
    }
	
	/**
	 * Method affecteds
	 *
	 * @param $resource $resource [explicite description]
	 *
	 * @return object
	 */
	public function affecteds()
    {
		if(!self::getConnection()){
            throw new \Exception("Não existe conexção ativa.");
        }

        return mysqli_affected_rows(self::getConnection());
    }
	
	/**
	 * Method close
	 *
	 * @return object
	 */
	public function close()
	{
		if(!self::getConnection()){
            throw new \Exception("Não existe conexção ativa.");
        }

		return mysqli_close(self::getConnection());
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
	protected static function setError(string $error)
	{
		if(isset($error) && !empty($error)){
			self::$error = $error;
		}
	}
}
