<?php

namespace douggonsouza\mvc\view;

use douggonsouza\mvc\view\mimes;
use douggonsouza\propertys\propertysInterface;

class display
{
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

        if(isset($params) && !empty($params)){
            $params = $params;
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
}
?>