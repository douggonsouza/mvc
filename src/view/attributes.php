<?php

namespace douggonsouza\mvc\view;

use douggonsouza\propertys\propertys;
use douggonsouza\mvc\view\attributesInterface;

class attributes extends propertys implements attributesInterface
{
    /**
     * Method get
     *
     * @param string $id [explicite description]
     *
     * @return self
     */
    public function get(string $id)
    {
        return $this->{$id};
    }

    /**
     * Method new
     *
     * @param string $id    [explicite description]
     * @param $value $value [explicite description]
     *
     * @return self
     */
    public function set(string $id, $value)
    {
        if(isset($value) && !empty($value)){
            $this->{$id} = $value;
        }

        return $this;
    }
    
    /**
     * Method delete
     *
     * @param string $id [explicite description]
     *
     * @return self
     */
    public function delete(string $id)
    {
        unset($this->{$id});

        return $this;
    }
    
    /**
     * Method session
     *
     * @return void
     */
    public function session()
    {
        $attributes = (array) $this;
        if(isset($attributes) && !empty($attributes)){
            foreach($attributes as $index => $value){
                $$index = $value;
                unset($this->{$index});
            }
        } 
    }
}

?>