<?php

class LazyLoader{
     private $providerOrValue;
     private $evaluated = false;

     public function __construct($providerOrValue) {
         $this->providerOrValue = $providerOrValue;
     }

     public function __invoke(){
         if (!$this->evaluated){
             if (is_callable($this->providerOrValue))
                 $this->providerOrValue = call_user_func ($this->providerOrValue);
             $this->evaluated = true;
         }
         return $this->providerOrValue;
     }
 }

