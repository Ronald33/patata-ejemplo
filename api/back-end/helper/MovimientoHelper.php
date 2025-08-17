<?php
abstract class MovimientoHelper
{
    public static function castToMovimiento($object, $set_sub_items = true, $id = NULL)
    {
        $className = NULL;
        switch($object->tipo)
        {
            case 'INGRESO': $className = 'Ingreso'; break;
            case 'EGRESO': $className = 'Egreso'; break;
        }
        assert($className != NULL);

        $movimiento = Helper::cast($className, $object);

        if($id === NULL)
        {
            $movimiento->setFecha(Helper::getCurrentTimestamp());
        }
        else
        {
            
        }

        if($set_sub_items)
        {
            $movimiento->setCuenta(CuentaHelper::castToCuenta($object->cuenta, isset($object->cuenta->id) ? $object->cuenta->id : NULL));

            if($id == NULL)
            {
                $movimiento->setUsuario(Helper::getCurrentUser());
            }
            else
            {
                $movimiento->setUsuario(UsuarioHelper::castToUsuario($object->usuario, true, isset($object->usuario->id) ? $object->usuario->id : NULL));

                if(isset($object->caja))
                {
                    $movimiento->setCaja(CajaHelper::castToCaja($object->caja, true, isset($object->caja->id) ? $object->caja->id : NULL));
                }
                
            }
        }

        return $movimiento;
    }

    public static function fillValidator($validator, $data, $id = NULL)
    {
        $validator->addInputFromObject('Monto', $data, 'monto')->addRule('isPositive');
        $extrasDAO = new ExtrasDAO();
        $tipos = $extrasDAO->getEnumValues('movimientos', 'movi_tipo')['data'];
        $validator->addInputFromObject('Tipo', $data, 'tipo')->addRule('isIn', $tipos);
        $validator->addInputFromObject('Descripcion', $data, 'descripcion', true)->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 256);

        $cuentaId = (isset($data->cuenta) && isset($data->cuenta->id)) ? $data->cuenta->id : NULL;
        $validator->addInput('Cuenta', $cuentaId)->addRule(['rowExists', 'Ingrese un cuenta válido'], 'cuentas', 'cuen_id');

        $user = Helper::getCurrentUser();
        if($user instanceof Administrador)
        {
            $cajaId = (isset($data->caja) && isset($data->caja->id)) ? $data->caja->id : NULL;
            $validator->addInput('Caja', $cajaId)->addRule(['rowExists', 'Ingrese un caja válido'], 'cajas', 'caja_id');
        }

        if($id != NULL) // For edit cases
        {

        }
    }

    public static function getCaja($object) { return Helper::cast('Caja', $object->caja); }

    public static function validatePaginationAndSort($validator)
    {
        Helper::validatePagination($validator);
        $validator->addInput('Columna de ordenamiento', NULL)->addCustomRule(self::getColumnToSort('sort') != NULL, 'Columna de ordenamiento inválida');
    }

    public static function getColumnToSort($key)
    {
        if(isset($_GET[$key]))
        {
            switch($_GET[$key])
            {
                case 'id': return 'movi_id';
                case 'monto': return 'movi_monto';
                case 'tipo': return 'movi_tipo';
                case 'fecha': return 'movi_fecha';
                case 'cuenta': return 'cuen_nombre';
                case 'persona': return 'pers_nombres';
                case 'terminal': return 'term_nombre';
                case 'descripcion': return 'movi_descripcion';
                default: return NULL;
            }
        }
        else { return NULL; }
    }

    public static function getWhereByGet()
    {
        $data = [];
        $query = [];

        $arguments = Repository::getURIDecoder()->getArguments();
        if(sizeof($arguments) > 0) { array_push($query, 'movi_id = :id'); $data['id'] = $arguments[0]; }

        foreach($_GET as $key => $value)
        {
            switch($key)
            {
                case 'monto': array_push($query, 'movi_monto / 100 = :monto'); $data['monto'] = $value; break;
                case 'tipo': array_push($query, 'movi_tipo = :tipo'); $data['tipo'] = $value; break;
                case 'start': array_push($query, 'UNIX_TIMESTAMP(movi_fecha) >= :start'); $data['start'] = $value; break;
                case 'end': array_push($query, 'UNIX_TIMESTAMP(movi_fecha) <= :end'); $data['end'] = $value; break;
                case 'cuenta': array_push($query, 'cuen_id = :cuenta'); $data['cuenta'] = $value; break;
                case 'persona':
                    array_push($query, 'pers_nombres LIKE :persona OR pers_apellidos LIKE :persona OR pers_documento LIKE :persona'); 
                    $data['persona'] = '%' . $value . '%'; break;
                case 'terminal': array_push($query, 'term_id = :terminal'); $data['terminal'] = $value; break;
                case 'descripcion': array_push($query, 'movi_descripcion LIKE :descripcion'); $data['descripcion'] = '%' . $value . '%'; break;
            }
        }

        return ['data' => $data, 'query' => $query];
    }

    public static function getOrderBy() { return 'ORDER BY ' . self::getColumnToSort('sort') . ' ' . ($_GET['reverse'] == 'true' ? 'DESC' : 'ASC'); }
}