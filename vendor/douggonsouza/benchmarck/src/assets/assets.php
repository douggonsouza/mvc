<?php

namespace douggonsouza\benchmarck\assets;

use douggonsouza\benchmarck\behaviorInterface;

class assets implements behaviorInterface
{
    const ABSOLUTE_DIR = 'absolute_dir';
    const ABSOLUTE_URL = 'absolute_url';
    const SELF_URL     = 'self_url';

    protected $host;
    protected $type = self::ABSOLUTE_DIR;

    /**
     * Executa o comportamento
     *
     * @param string|null $script
     * 
     * @return void
     * 
     */
    public function behavior(string $script = null)
    {
        $local = null;
        $protocol = explode('/',$_SERVER['SERVER_PROTOCOL']);
        $this->setHost(strtolower($protocol[0]) . '://' . $_SERVER['HTTP_HOST']);

        switch($this->getType()){
            case self::ABSOLUTE_URL:
                $fullDir = __DIR__ . $script;
                if(!file_exists($fullDir)){
                    throw new \Exception("Arquivo não encontrado.");
                }
                $local = str_replace($_SERVER['DOCUMENT_ROOT'], $this->getHost(), $fullDir);
                break;
            case self::SELF_URL:
                $fullDir = $_SERVER['DOCUMENT_ROOT'] . $script;
                if(!file_exists($fullDir)){
                    throw new \Exception("Arquivo não encontrado.");
                }
                $local = str_replace($_SERVER['DOCUMENT_ROOT'], $this->getHost(), $fullDir);
                break;
            default:
                $fullDir = __DIR__ . $script;
                if(!file_exists($fullDir)){
                    throw new \Exception("Arquivo não encontrado.");
                }
                $local = $fullDir;
                break;
        }

        return $local;
    }

    /**
     * Get the value of host
     */ 
    private function getHost()
    {
        return $this->host;
    }

    /**
     * Set the value of host
     *
     * @return  self
     */ 
    private function setHost($host)
    {
        if(isset($host) && !empty($host)){
            $this->host = $host;
        }

        return $this;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        if(isset($type) && !empty($type)){
            $this->type = $type;
        }

        return $this;
    }
}
?>