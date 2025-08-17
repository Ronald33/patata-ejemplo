<?php
abstract class LogHelper
{
    public static function castToLog($object, $id = NULL)
    {
        $log = Helper::cast('Log', $object);

        $log->setUsuario(Helper::cast('Usuario', $object->usuario));

        if($id === NULL)
        {
            // Set values by default
        }
        else
        {

        }

        return $log;
    }
}