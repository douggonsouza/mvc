<?php

namespace douggonsouza\mvc\view;

use douggonsouza\mvc\view\mimes;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\mvc\view\screensInterface;
use douggonsouza\mvc\view\attributesInterface;
use douggonsouza\mvc\view\attributes;
use douggonsouza\mvc\view\templatesInterface;

abstract class screens implements screensInterface
{
    protected static $propertys;
    protected static $benchmarck;
    protected static $attributes;
    protected static $layout;
    protected static $page;
    protected static $templateLayout;
    protected static $templateBlock;
    protected static $templatePage;

    /**
     * Responde com a inclusão do arquivo
     * 
     * @param string    $local
     * @param object    $param
     * 
     * @return bool|int
     */
    final function body(string $local, propertysInterface $params = null)
    {
        if(!file_exists($local)){
            return 500;
        }

        if(isset(self::$attributes)){
            self::getAttributes()->session();
        }

        if(isset($params) && !empty($params)){
            $variables = (array) $params;
            foreach($variables as $index => $value){
                $$index = $value;
            }
        }

        return include($local);
	}
               
    /**
	 * Responde com o conteúdo do arquivo
     * 
	 * @param string $local
     * 
     * @return string|int
	 */
    final function output(string $local)
    {
        if(file_exists($local)){
            return file_get_contents($local);
        }

        return 500;
    }

    /**
    * Carrega o local da identificação da requisição
    *
    * @param string                  $identify
    * @param behaviorInterface       $config
    * @param propertysInterface|null $params
    * 
    * @return void
    * 
    */
    public function identified(string $identify, propertysInterface $params = null, string $layout = null)
    {
        if(isset($params)){
            $this->setPropertys($params);
        }

        $this->identifyLayout($layout);
        $this->setPage($this->getBenchmarck()->identified($identify));

        $this->body($this->getLayout(), $this->getPropertys());
        return;
    }

    /**
     * Set the value of identifyLayout
     *
     * @return  self
     */ 
    public function identifyLayout(string $identifyLayout = null)
    {
        if(isset($identifyLayout) && !empty($identifyLayout)){
            $this->setLayout($this->getBenchmarck()->identified($identifyLayout));
        }

        return $this;
    }

    /**
     * Prepara cabeçalho do arquivo de saída
     *
     * @param string  $local
     * @param string  $filename
     * @param boolean $binary
     * @param boolean $download
     * @return void
     */
    protected function headered(string $local, string $filename, bool $binary = false, bool $download = false)
    {
        $mimes = new mimes();
        $ext   = end(explode('.',$filename));
        if(!$mimes->get($ext)){
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($local)).' GMT', true, 200);
        $this->contentHeader(
            $local,
            str_replace($ext,'',$filename),
            $ext,
            $binary,
            $download
        );
    }

    /**
     * Undocumented function
     *
     * @param string  $local
     * @param string  $filename
     * @param string  $ext
     * @param boolean $binary
     * @param boolean $download
     * @return void
     */
    protected function contentHeader(string $local, string $filename, string $ext, bool $binary = false, bool $download = false)
    {
        $mimes = new mimes();

        header('Content-Length: '.filesize($local));
        header('Content-type: '. $mimes->get($ext));
        if($binary){
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: application/octet-stream');
        }
        if($download)
            header('Content-Disposition: attachment; filename="'.$filename.$ext.'"');
    }
    
    /**
     * Method attributes
     *
     * @return object
     */
    public static function attributes()
    {
        if(!isset(self::$attributes)){
            self::setAttributes(new attributes());
        }

        return self::getAttributes();
    }

    /**
     * Get the value of page
     */ 
    public static function getPage()
    {
        return self::$page;
    }

    /**
     * Set the value of page
     *
     * @return  self
     */ 
    public static function setPage($page)
    {
        if(isset($page) && !empty($page)){
            self::$page = $page;
        }
    }

    /**
     * Get the value of benchmarck
     */ 
    public function getBenchmarck()
    {
        return self::$benchmarck;
    }

    /**
     * Set the value of benchmarck
     *
     * @return  self
     */ 
    public static function setBenchmarck($benchmarck)
    {
        if(isset($benchmarck) && !empty($benchmarck)){
            self::$benchmarck = $benchmarck;
        }
    }

    /**
     * Get the value of propertys
     */ 
    public function getPropertys()
    {
        return self::$propertys;
    }

    /**
     * Set the value of propertys
     *
     * @return  self
     */ 
    public static function setPropertys(propertysInterface $propertys)
    {
        if(isset($propertys) && !empty($propertys)){
            self::$propertys = $propertys;
        }
    }

    /**
     * Get the value of layout
     */ 
    public static function getLayout()
    {
        return self::$layout;
    }

    /**
     * Set the value of layout
     *
     * @return  self
     */ 
    public static function setLayout($layout)
    {
        if(isset($layout) && !empty($layout)){
            self::$layout = $layout;
        }
    }

    /**
     * Get the value of attributes
     */ 
    public static function getAttributes()
    {
        return self::$attributes;
    }

    /**
     * Set the value of attributes
     *
     * @return  self
     */ 
    public static function setAttributes(attributesInterface $attributes)
    {
        if(isset($attributes) && !empty($attributes)){
            self::$attributes = $attributes;
        }
    }

    /**
     * Get the value of templateLayout
     */ 
    public static function getTemplateLayout()
    {
        return self::$templateLayout;
    }

    /**
     * Set the value of templateLayout
     *
     * @return  void
     */ 
    public static function setTemplateLayout(templatesInterface $templateLayout)
    {
        if(isset($templateLayout) && !empty($templateLayout)){
            self::$templateLayout = $templateLayout;
        }
    }

    /**
     * Get the value of templatePage
     */ 
    public static function getTemplatePage()
    {
        return self::$templatePage;
    }

    /**
     * Set the value of templatePage
     *
     * @return  void
     */ 
    public static function setTemplatePage(templatesInterface $templatePage)
    {
        if(isset($templatePage) && !empty($templatePage)){
            self::$templatePage = $templatePage;
        }
    }

    /**
     * Get the value of templateBlock
     */ 
    public static function getTemplateBlock()
    {
        return self::$templateBlock;
    }

    /**
     * Set the value of templateBlock
     *
     * @return  self
     */ 
    public static function setTemplateBlock(templatesInterface $templateBlock)
    {
        if(isset($templateBlock) && !empty($templateBlock)){
            self::$templateBlock = $templateBlock;
        }
    }
}
?>