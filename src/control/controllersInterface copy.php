<?php

namespace douggonsouza\mvc\control;

use douggonsouza\propertys\propertysInterface;

interface controllersAdapterInterface
{
    /**
     * Method requestController
     *
     * @param string $controller [explicite description]
     * @param propertysInterface $infos [explicite description]
     * @param string $function [explicite description]
     *
     * @return void
     */
    public function requestController(string $controller, propertysInterface $infos, string $function = 'main');
 
    /**
     * Method requestView
     *
     * @param string $template [explicite description]
     * @param propertysInterface $infos [explicite description]
     *
     * @return void
     */
    public function requestView(string $template, propertysInterface $infos);
}

?>