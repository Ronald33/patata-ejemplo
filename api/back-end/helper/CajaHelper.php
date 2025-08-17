<?php
abstract class CajaHelper
{
    public static function castToCaja($object, $set_sub_items = true, $id = NULL)
    {
        $caja = Helper::cast('Caja', $object);

        if($id === NULL)
        {
            $caja->setApertura(Helper::getCurrentTimestamp());
        }
        else
        {
            
        }

        if($set_sub_items)
        {
            $caja->setTerminal(TerminalHelper::castToTerminal($object->terminal, true, isset($object->terminal->id) ? $object->terminal->id : NULL));
            if(isset($object->cierreUsuario)) { $caja->setCierreUsuario(UsuarioHelper::castToUsuario($object->cierreUsuario, true, isset($object->cierreUsuario->id) ? $object->cierreUsuario->id : NULL)); }

            $_saldos = [];
            foreach($object->saldos as $saldo) { array_push($_saldos, SaldoHelper::castToSaldo($saldo, true, isset($saldo->id) ? $saldo->id : NULL)); }

            if($id === NULL)
            {
                $caja->setAperturaUsuario(Helper::getCurrentUser());

                foreach($_saldos as $saldo)
                {
                    if($saldo->getCuenta()->getId() != 1) { $saldo->setInicial(0); }
                }
            }
            else
            {
                $caja->setAperturaUsuario(UsuarioHelper::castToUsuario($object->aperturaUsuario, true, isset($object->aperturaUsuario->id) ? $object->aperturaUsuario->id : NULL));
            }

            $caja->setSaldos($_saldos);
        }

        return $caja;
    }

    public static function fillValidator($validator, $data, $id = NULL)
    {
        $terminalId = (isset($data->terminal) && isset($data->terminal->id)) ? $data->terminal->id : NULL;
        $validator->addInput('Terminal', $terminalId)->addRule(['rowExists', 'Ingrese un terminal válido'], 'terminales', 'term_id');
 
        $cantidad_de_saldos = (isset($data->saldos) && is_array($data->saldos)) ? sizeof($data->saldos) : 0;
        $validator->addInput('Saldos registrados')->addCustomRule($cantidad_de_saldos > 0, 'La caja debe de tener al menos un saldo');

        $counter = 0;
        foreach($data->saldos as $saldo)
        {
            $counter++;
            $cuenta = NULL;
            $cuentaDAO = new CuentaDAO();
            if(isset($saldo->cuenta) && isset($saldo->cuenta->id)) { $cuenta = $cuentaDAO->selectById($saldo->cuenta->id); }

            if($cuenta)
            {
                if($cuenta->getId() == 1) { $validator->addInputFromObject('Saldo ' . $counter, $saldo, 'inicial')->addRule('isPositiveOrZero'); }
            }
            else { $validator->addInput('Cuenta')->addCustomRule(isset($cuenta), 'Ingrese una cuenta válida'); }
        }

        if($id != NULL) // For edit cases
        {

        }
    }
}