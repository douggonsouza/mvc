<?php

namespace douggonsouza\mvc\control;

use douggonsouza\propertys\propertysInterface;

interface responsesAdapterInterface
{ 
    /**
     * Method way
     *
     * @param string             $response [explicite description]
     * @param propertysInterface $infos    [explicite description]
     *
     * @return void
     */
    public function way(string $response, propertysInterface $infos);

}

?>