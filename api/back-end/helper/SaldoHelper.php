<?php
abstract class SaldoHelper
{
    public static function castToSaldo($object, $set_sub_items = true, $id = NULL)
    {
        $saldo = Helper::cast('Saldo', $object);

        if($id === NULL)
        {
            $saldo->setActual($object->inicial);
        }
        else
        {

        }

        if($set_sub_items)
        {
            $saldo->setCuenta(CuentaHelper::castToCuenta($object->cuenta, true, isset($object->cuenta->id) ? $object->cuenta->id : NULL));

            if($id === NULL)
            {
                
            }
            else
            {

            }
        }

        return $saldo;
    }
}