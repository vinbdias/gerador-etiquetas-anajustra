<?php 

    abstract class Model {

        /**
         * Método "mágico" getter
         */
        public function __get($name) {

            if(isset($this->$name)) {

                return $this->$name;
            }

            $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return null;
        }

        /**
         * Método "mágico" setter
         */
        public function __set($name, $value) {

            $this->$name = $value;
        }       

        /**
         * Método "mágico" invocado quando as funções isset ou empty são chamadas para uma propriedade do objeto
         */
        public function __isset($name){

            return isset($this->$name);
        }         
    }