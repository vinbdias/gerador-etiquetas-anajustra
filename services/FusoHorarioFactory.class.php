<?php 
    
    define('DEFAULT_TIMEZONE_FACTORY', 'America/Sao_Paulo');

    abstract class FusoHorarioFactory {

        static function getFusoHorario() {

            return new DateTimeZone(DEFAULT_TIMEZONE_FACTORY);
        }
    }