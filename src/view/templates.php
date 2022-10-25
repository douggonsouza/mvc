<?php

namespace douggonsouza\mvc\view;

use douggonsouza\mvc\view\templatesInterface;

class templates implements templatesInterface
{
    protected $type;
    protected $template;
    
    /**
     * Method __construct
     *
     * @param templatesInterface $template [explicite description]
     * @param screensInterface   $screen [explicite description]
     *
     * @return void
     */
    public function __construct(string $template, string $type = 'block')
    {
        $this->setTemplate($template);
        $this->setType($type);
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

    /**
     * Get the value of template
     */ 
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the value of template
     *
     * @return  self
     */ 
    public function setTemplate($template)
    {
        if(isset($template) && !empty($template)){
            $this->template = $template;
        }

        return $this;
    }
}

?>