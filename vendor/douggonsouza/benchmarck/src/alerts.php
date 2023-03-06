<?php

namespace douggonsouza\benchmarck;

use douggonsouza\benchmarck\alertsInterface;

class alerts implements alertsInterface
{
    // BADGE
    const BADGE_PRIMARY    = 'Primary';
    const BADGE_SECONDARY  = 'Secondary';
    const BADGE_SUCCESS    = 'Success';
    const BADGE_DANGER     = 'Danger';
    const BADGE_WARNING    = 'Warning';
    const BADGE_INFO       = 'Info';
    const BADGE_LIGHT      = 'Light';
    const BADGE_DARK       = 'Dark';

    // LABEL BADGE
    const LABEL_BADGE = array(
        self::BADGE_PRIMARY    => 'alert-primary',
        self::BADGE_SECONDARY  => 'alert-secondary',
        self::BADGE_SUCCESS    => 'alert-success',
        self::BADGE_DANGER     => 'alert-danger',
        self::BADGE_WARNING    => 'alert-warning',
        self::BADGE_INFO       => 'alert-info',
        self::BADGE_LIGHT      => 'alert-light',
        self::BADGE_DARK       => 'alert-dark'
    );

    static public $clear = true;
    static public $name;
    
    /**
     * __construct - Evanto construtor da classe
     *
     * @param  string $name
     * @return void
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * Busca pela mensagem de alerta na sessão
     * 
     * 
     * @return array
     */
    static final function session()
    {
        if(!isset($_SESSION[self::getName()]) || empty($_SESSION[self::getName()])){
            $_SESSION[self::getName()] = array();
        }

        return $_SESSION[self::getName()];
    }

    /**
     * Salva na sessão a mensagem de alerta
     *
     * @param string $alert
     * @param string $badge
     * @return void
     */
    public final function save(string $alert, string $badge = self::BADGE_SUCCESS)
    {
        if(!isset($alert) || empty($alert)){
            return false;
        }

        $_SESSION[self::getName()][$badge][] = self::template($alert, $badge);

        return true;
    }

    /**
     * Expõe template para do alerta conforme o layout
     * 
     * <div class="sufee-alert alert with-close alert-primary alert-dismissible fade show">
     * 		You successfully read this important alert.
     * 		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
     * 		<span aria-hidden="true">&times;</span></button>
     * 	</div>
     *
     * @return void
     */
    public static function template(string $message, string $badge = self::BADGE_SUCCESS)
    {
        if(!isset($message) || empty($message)){
            return null;
        }

        return sprintf('<div class="sufee-alert alert with-close %1$s alert-dismissible fade show">
             %2$s
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>',
        self::LABEL_BADGE[$badge],
        $message
        );
    }

    /**
     * Regista um alerta
     *
     * @param string  $mensagem
     * @param string  $badge
     * @return void
     */
    static final public function set(string $mensagem, string $badge = self::BADGE_SUCCESS)
    {
        self::save(str_pad($mensagem,120), $badge);
    }

    /**
     * Retorna alerta definido
     * @param bool $clear
     * 
     * @return mixed
     */
    static final public function get(bool $clear = true)
    {
        $alerts = self::session();

        if(!isset($alerts) || empty($alerts)){
            return null;
        }

        self::setClear($clear);
        if(self::getClear()){
            self::clear();
        }

        $messages = null;
        foreach($alerts as $items){
            $messages = implode("\n ",$items)."\n ";
        }

        return $messages;
    }
   
    /**
     * clear - Limpa a mensagem de alerta
     *
     * @param  mixed $clear
     * @return void
     */
    static final public function clear($clear = null)
    {
        self::setClear($clear);
        if(self::getClear()){
            $_SESSION[self::getName()] = array();
        }
    }

    /**
     * Get the value of exists
     */ 
    static final public function exist()
    {
        if(isset($_SESSION[self::getName()]) && !empty($_SESSION[self::getName()])){
             return true;
        }

        return false;
    }

    /**
     * Get the value of clear
     */ 
    static public function getClear()
    {
        return self::$clear;
    }

    /**
     * Set the value of clear
     *
     * @return  self
     */ 
    static public function setClear($clear)
    {
        if(isset($clear)){
            self::$clear = $clear;
        }
    }

    /**
     * Evento destrutor da classe
     */
    public function __destruct()
    {
        self::clear();
    }

    /**
     * Get the value of name
     */ 
    static public function getName()
    {
        return self::$name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    static private function setName($name)
    {
        if(isset($name) && !empty($name)){
            self::$name = preg_replace(array(
                    "/(á|à|ã|â|ä)/",
                    "/(Á|À|Ã|Â|Ä)/",
                    "/(é|è|ê|ë)/",
                    "/(É|È|Ê|Ë)/",
                    "/(í|ì|î|ï)/",
                    "/(Í|Ì|Î|Ï)/",
                    "/(ó|ò|õ|ô|ö)/",
                    "/(Ó|Ò|Õ|Ô|Ö)/",
                    "/(ú|ù|û|ü)/",
                    "/(Ú|Ù|Û|Ü)/",
                    "/(ñ)/",
                    "/(Ñ)/",
                    "/(ç)/",
                    "/(Ç)/"),
                explode(" ","a A e E i I o O u U n N c C"),
                $name
            );
        }
    }
}

    