<?php
        
    require_once(__ROOT__ . DS . 'services' . DS . 'FusoHorarioFactory.class.php');

    define('DEFAULT_TIME_FACTORY', 'now');

    abstract class DataHoraFactory {

        static function getDataHora() {

            return new DateTime(DEFAULT_TIME_FACTORY, FusoHorarioFactory::getFusoHorario());
        }
    }