<?php

namespace douggonsouza\mvc\control;

use douggonsouza\mvc\control\exiting;

abstract class act extends exiting
{
    protected $intoDIRs;

    protected $dir;
    protected $namespace;
    protected $url;

    /**
     * Função a ser executada no contexto da action
     *
     * @param array $info
     * @return void
     */
    abstract public function main(array $info);

    /**
     * Definições para arquivos e diretórios
     *
     * @param string $template
     * @param string $layout
     * @param string $assets
     * @return void
     * @version 1.0.0
     * @deprecated 1.0.0
     */
    protected function defines($layout, $urlAssets)
    {
        $this->setLayout($layout);
    }

    /**
     * Informações para arquivos e diretórios
     *
     * @param string|null $layout
     * @param string|null $urlAssets
     * @param string|null $template
     * @return void
     * @version 1.1.0
     */
    final function infos(string $layout = null, string $urlAssets, string $template = null)
    {
        $this->setLayout($layout);
        $this->setAssets($urlAssets);
        $this->setTemplate($template);
    }

    /**
     * Requisita o template na raiz da VIEW
     * @param string $local
     * @return type
     */
    public function partial($controller, $params = array())
    {
        if(!class_exists($controller)){
            throw new \Exception('Inexistência da classe em memória.');
        }

        return router::redirectAction($controller, $params);
    }

    /**
     * Para ser disparado antes
     *
     * @return void
     */
    public function _before()
    {
        return null;
    }


    /**
     * Para ser disparado depois
     *
     * @return void
     */
    public function _after()
    {
        return null;
    }

    /**
     * Get the value of local
     */ 
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set the value of local
     *
     * @return  self
     */ 
    public function setLocal($local)
    {
        if(isset($local) && !empty($local)){
            $this->local = $local;
        }
        return $this;
    }

    /**
     * Get the value of intoDIRs
     */ 
    public function getIntoDIRs()
    {
        if(!isset($this->intoDIRs) || empty($this->intoDIRs)){
            $this->setIntoDIRs(new intoDIRs($this->dir, $this->namespace, $this->url));
        }
        return $this->intoDIRs;
    }

    /**
     * Set the value of intoDIRs
     *
     * @return  self
     */ 
    protected function setIntoDIRs(intoDIRsInterface $intoDIRs)
    {
        if(isset($intoDIRs) && !empty($intoDIRs)){
            $this->intoDIRs = $intoDIRs;
        }

        return $this;
    }
}