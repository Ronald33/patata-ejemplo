<?php
abstract class CuentaHelper
{
    public static function castToCuenta($object, $id = NULL)
    {
        $cuenta = Helper::cast('Cuenta', $object);


        if($id === NULL)
        {
            // Set values by default
        }
        else
        {

        }

        return $cuenta;
    }

    public static function fillValidator($validator, $data, $id = NULL)
    {
        $validator->addInputFromObject('Nombre', $data, 'nombre')->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 32)->addRule('isUnique', 'cuentas', 'cuen_nombre', isset($id) ? 'cuen_id != ' . $id : '1');


        if($id != NULL) // For edit cases
        {

        }
    }
}