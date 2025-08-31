<?php
class MovimientoDAO
{
    private static $table = 'movimientos';
    private static $pk = 'movi_id';
    private static $separator = '_pfw_';

    public static function getSelectedFields($alias = NULL, $prefix = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return [
            $alias . 'movi_id' => $prefix . 'id',
            $alias . 'movi_monto' => $prefix . 'monto',
            $alias . 'movi_tipo' => $prefix . 'tipo',
            'UNIX_TIMESTAMP(' . $alias . 'movi_fecha)' => $prefix . 'fecha',
            $alias . 'movi_descripcion' => $prefix . 'descripcion',
            $alias . 'movi_usua_id' => $prefix . 'usuario_id',
            $alias . 'movi_cuen_id' => $prefix . 'cuenta_id',
            $alias . 'movi_caja_id' => $prefix . 'caja_id',
        ];
    }

    private static function getFieldsToInsert(Movimiento $movimiento, $caja_id = NULL)
    {
        $tipo = NULL;
        switch(get_class($movimiento))
        {
            case Ingreso::class: $tipo = 'INGRESO'; break;
            case Egreso::class: $tipo = 'EGRESO'; break;
        }
        assert($tipo != NULL);

        $fields = 
        [
            'movi_monto' => $movimiento->getMonto(),
            'movi_tipo' => $tipo,
            'movi_fecha' => Helper::getDateTimeFromTimestamp($movimiento->getFecha()), 
            'movi_descripcion' => $movimiento->getDescripcion(),
            'movi_usua_id' => $movimiento->getUsuario()->getId(), 
            'movi_cuen_id' => $movimiento->getCuenta()->getId(), 
        ];

        if($caja_id != NULL) { $fields['movi_caja_id'] = $caja_id; }

        return $fields;
    }

    private function processRow(&$row, $key, $params = [])
    {
        $grouped = Helper::getGroupedEntities($row, self::$separator);
        $row = $grouped['movimiento'];

        $row->id = (int) $row->id;
        $row->monto = (int) $row->monto;
        $row->fecha = (int) $row->fecha;

        $cast = isset($params['cast']) ? $params['cast'] : false;
        $set_sub_items = isset($params['set_sub_items']) ? $params['set_sub_items'] : false;

        if($set_sub_items)
        {
            $cajaGlobal = isset($params['caja']) ? $params['caja'] : NULL;

            $cuenta = $grouped['cuenta'];
            $usuario = $grouped['usuario'];
            $persona = $grouped['persona'];

            if($cajaGlobal) { $caja = clone $cajaGlobal; $terminal = clone $cajaGlobal->terminal; }
            else { $caja = $grouped['caja']; $terminal = $grouped['terminal']; }

            if($cast)
            {
                $row = Helper::cast($row->tipo == 'INGRESO' ? 'Ingreso' : 'Egreso', $row);

                if(isset($caja->id) && isset($terminal->id))
                {
                    $caja = Helper::cast('Caja', $caja);
                    $caja->setTerminal(Helper::cast('Terminal', $terminal));
                    $row->setCaja($caja);
                }

                $usuario = Helper::cast($usuario->tipo == 'ADMINISTRADOR' ? 'Administrador' : 'Operador', $usuario);
                $usuario->setPersona(Helper::cast('Persona', $persona));
                $row->setCuenta(Helper::cast('Cuenta', $cuenta));
                
                $row->setUsuario($usuario);
            }
            else
            {
                if(isset($caja->id) && isset($terminal->id)) { $caja->terminal = $terminal; $row->caja = $caja; }
                
                $usuario->persona = $persona;
                $row->usuario = $usuario;
                $row->cuenta = $cuenta;
            }
        }
    }

    public function selectAll($cast = true, $set_sub_items = true)
    {
        $db = Repository::getDB();

        $fields = self::getSelectedfields(NULL, 'movimiento' . self::$separator);
        $join = '';

        if($set_sub_items)
        {
            $fields = array_merge($fields, CuentaDAO::getSelectedFields(NULL, 'cuenta' . self::$separator), UsuarioDAO::getSelectedFields(NULL, 'usuario' . self::$separator), PersonaDAO::getSelectedFields(NULL, 'persona' . self::$separator), CajaDAO::getSelectedFields(NULL, 'caja' . self::$separator), TerminalDAO::getSelectedFields(NULL, 'terminal' . self::$separator));
            $join .= ' JOIN cuentas ON movi_cuen_id = cuen_id JOIN usuarios ON movi_usua_id = usua_id JOIN personas ON usua_pers_id = pers_id JOIN cajas ON movi_caja_id = caja_id JOIN terminales ON caja_term_id = term_id';
        }

        $where = MovimientoHelper::getWhereByGet();
        $results = $db->select(self::$table . $join, $fields, implode(' AND ', $where['query']), $where['data'], implode(' ', [MovimientoHelper::getOrderBy(), Helper::getLimit()]));
        array_walk($results, [$this, 'processRow'], ['cast' => $cast, 'set_sub_items' => $set_sub_items]);
        $total = $db->selectOne(self::$table . $join, 'COUNT(1) AS total', implode(' AND ', $where['query']), $where['data'])->total;
        return ['data' => $results, 'total' => $total];
    }

    public function selectByCajaId($caja_id, $cast = true, $set_sub_items = true)
    {
        $db = Repository::getDB();

        $fields = self::getSelectedfields(NULL, 'movimiento' . self::$separator);
        $join = '';

        if($set_sub_items)
        {
            $fields = array_merge($fields, CuentaDAO::getSelectedFields(NULL, 'cuenta' . self::$separator), UsuarioDAO::getSelectedFields(NULL, 'usuario' . self::$separator), PersonaDAO::getSelectedFields(NULL, 'persona' . self::$separator));
            $join .= ' JOIN cuentas ON movi_cuen_id = cuen_id JOIN usuarios ON movi_usua_id = usua_id JOIN personas ON usua_pers_id = pers_id';
        }

        $where = MovimientoHelper::getWhereByGet();
        array_push($where['query'], 'movi_caja_id = :caja_id');
        $where['data']['caja_id'] = $caja_id;
        $results = $db->select(self::$table . $join, $fields, implode(' AND ', $where['query']), $where['data'], implode(' ', [MovimientoHelper::getOrderBy(), Helper::getLimit()]));

        $cajaDao = new CajaDAO();
        array_walk($results, [$this, 'processRow'], ['cast' => $cast, 'set_sub_items' => $set_sub_items, 'caja' => $cajaDao->selectById($caja_id, false, true)]);
        $total = $db->selectOne(self::$table . $join, 'COUNT(1) AS total', implode(' AND ', $where['query']), $where['data'])->total;
        return ['data' => $results, 'total' => $total];
    }

    public function selectById($id, $cast = true, $set_sub_items = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'movi_id = :id', ['id' => $id]);
        if($result) { $this->processRow($result, 0, ['cast' => $cast, 'set_sub_items' => $set_sub_items]); }
        return $result;
    }

    public function insert(Movimiento $movimiento, $caja_id)
    {
        $db = Repository::getDB();
        $db->insert(self::$table, self::getFieldsToInsert($movimiento, $caja_id));
        $movimiento->setId($db->getLastInsertId());

        // $saldoDAO = new SaldoDAO();
        // $saldoDAO->updateActual($movimiento, $caja_id);

        return true;
    }
}