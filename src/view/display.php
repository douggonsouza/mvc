<?php

namespace douggonsouza\mvc\view;

use douggonsouza\mvc\view\mimes;

class display{

    protected $label = null;
	
	/**
	 * Responde com o conteúdo do arquivo
	 * @param string $local
	 */
    final function body($local, $params = null)
    {
        if(!file_exists($local)){
            return '';
        }

        if(isset($params) && !empty($params)){
            foreach($params as $key => $vle){
                $$key = $vle;
            }                        
        }
        
        // label
        $this->label();

        return include($local);

	}

    /**
     * Undocumented function
     *
     * @param string $class
     * @return void
     */
    public function label(){
        if(!empty($this->getLabel())){
            print("\n<!-- action: ".$this->getLabel()." -->\n");
        }
        
    }
               
    /**
	 * Responde com o conteúdo do arquivo
	 * @param string $local
	 */
    final function output($local)
    {
        if(file_exists($local))
            return file_get_contents($local);
        return '';
    }
    
    /**
     * Renderiza arquivo de saída
     *
     * @param string $localRequest
     * @return object
     */
    final function render(string $localRequest)
    {
        if(!isset($localRequest))
            throw new \Exception('Not found object request.');
		$local = str_replace(
			array('/','//','\\','\\\\'),
			'/',
			DIR_ROOT.'/'.$localRequest);
        if(!file_exists($local)){
            header("HTTP/1.0 404 Not Found");
            exit;
        }
            
        $this->headered(
            str_replace(basename($local), '', $local),
            basename($local),
            true,
            true
        );

        return readfile($local);
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
     * Get the value of label
     */ 
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */ 
    protected function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}

?>