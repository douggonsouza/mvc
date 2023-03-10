<?php

namespace douggonsouza\mvc\view;

use douggonsouza\mvc\view\mimes;
use douggonsouza\propertys\propertys;
use douggonsouza\propertys\propertysInterface;
use douggonsouza\mvc\view\screensInterface;
use douggonsouza\mvc\view\attributesInterface;
use douggonsouza\mvc\view\attributes;
use douggonsouza\mvc\view\templatesInterface;

abstract class screens implements screensInterface
{
    protected static $propertys;
    protected static $benchmarck;
    protected static $params;
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

        if(isset($params)){
            self::setParams($params);
        }

        if(self::getParams()->exist()){
            $variables = self::getParams()->toArray();
            foreach($variables as $index => $value){
                ${$index} = $value;
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
            self::setParams($params);
        }

        // define local layout
        if(isset($layout) && !empty($layout)){
            if(!self::local($layout, 'layout')){
                throw new \Exception("Erro durante o carregamento do template de layout");
            }
        }

        // define local page
        if(!self::local($identify)){
            throw new \Exception("Erro durante o carregamento do template da página");
        }

        $this->body(self::getTemplateLayout()->getTemplate(), $this->getParams());
        return;
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
     * Method local
     *
     * @param string $template 
     * @param string $type 
     *
     * @return bool
     */
    protected static function local(string $template, string $type = 'page')
    {
        if(!isset($template) || empty($template)){
            throw new \Exception("Template não informado.");
        }

        // define local block
        switch($type){
            case 'block':
                $tmplt = self::getBenchmarck()->identified($template);
                if(!isset($tmplt) || empty($tmplt)){
                    $tmplt = $template;
                }
                self::setTemplateBlock(
                    new templates($tmplt, 'block')
                );
                break;
            case 'page':
                $tmplt = self::getBenchmarck()->identified($template);
                if(!isset($tmplt) || empty($tmplt)){
                    $tmplt = $template;
                }
                self::setPage($tmplt);
                self::setTemplatePage(
                    new templates(self::getPage(), 'page')
                );
                break;
            case 'layout':
                $tmplt = self::getBenchmarck()->identified($template);
                if(!isset($tmplt) || empty($tmplt)){
                    $tmplt = $template;
                }
                self::setLayout($tmplt);
                self::setTemplateLayout(
                    new templates(self::getLayout(), 'layout')
                );
                break;
            default:
                $tmplt = self::getBenchmarck()->identified($template);
                if(!isset($tmplt) || empty($tmplt)){
                    $tmplt = $template;
                }
                self::setPage($tmplt);
                self::setTemplatePage(
                    new templates(self::getPage(), 'page')
                );
                break;
        }

        return true;
    }
    
    /**
     * Method attributes
     *
     * @return object
     */
    public static function attributes(string $name, $value)
    {
        if(isset($value) && !empty($value)){
            self::setParams(new propertys(array($name => $value)));
        }

        return true;
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

    /**
     * Get the value of params
     */ 
    public static function getParams()
    {
        return self::$params;
    }

    /**
     * Set the value of params
     *
     * @return  self
     */ 
    public static function setParams(propertysInterface $params)
    {
        if(isset($params) && !empty($params)){
            if(!isset(self::$params)){
                self::$params = $params;
                return;
            }
            self::getParams()->add((array) $params);
        }
    }
}
?>