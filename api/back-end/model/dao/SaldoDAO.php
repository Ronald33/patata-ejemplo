<?php
class SaldoDAO
{
    private static $table = 'saldos';
    private static $pk = 'sald_id';

    public static function getSelectedFields($alias = NULL, $prefix = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return [
            $alias . 'sald_id' => $prefix . 'id',
            $alias . 'sald_inicial' => $prefix . 'inicial',
            $alias . 'sald_actual' => $prefix . 'actual',
            $alias . 'sald_cuen_id' => $prefix . 'cuenta_id',
        ];
    }

    private static function getFieldsToInsert(Saldo $saldo, $caja_id = NULL)
    {
        $fields = 
        [
            'sald_inicial' => $saldo->getInicial(),
            'sald_actual' => $saldo->getActual(),
            'sald_cuen_id' => $saldo->getCuenta()->getId(), 
        ];

        if($caja_id != NULL) { $fields['sald_caja_id'] = $caja_id; }

        return $fields;
    }

    private function processRow(&$row, $key, $params = [])
    {
        $row->id = (int) $row->id;
        $row->inicial = (int) $row->inicial;
        
        $row->actual = (int) $row->actual;
        

        $cast = isset($params['cast']) ? $params['cast'] : false;
        $set_sub_items = isset($params['set_sub_items']) ? $params['set_sub_items'] : false;

        if($set_sub_items)
        {
            $cuentaDAO = new CuentaDAO();
            $row->cuenta = $cuentaDAO->selectById($row->cuenta_id, false);
            
            unset($row->cuenta_id); 
        }

        if($cast) { $row = SaldoHelper::castToSaldo($row, $row->id); }
    }

    public function selectByCajaid($caja_id, $cast = true, $set_sub_items = true)
    {
        $db = Repository::getDB();
        $results = $db->select(self::$table, self::getSelectedFields(), 'sald_caja_id = :caja_id', ['caja_id' => $caja_id], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast, 'set_sub_items' => $set_sub_items]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function insert(Saldo $saldo)
    {
        $db = Repository::getDB();
        $db->insert(self::$table, self::getFieldsToInsert($saldo));
        $saldo->setId($db->getLastInsertId());
        return true;
    }

    public function insertFromCaja($caja)
    {
        $saldos = $caja->getSaldos();

        foreach($saldos as $saldo)
        {
            $data = self::getFieldsToInsert($saldo);
            $data['sald_caja_id'] = $caja->getId();
            if(!Repository::getDB()->insert(self::$table, $data)) { return false; }
        }

        return true;
    }

    public function updateActual(Movimiento $movimiento, $caja_id)
    {
        $db = Repository::getDB();
        $db->query('UPDATE saldos SET sald_actual = sald_actual ' . (get_class($movimiento) == 'Ingreso' ? '+' : '-') .' :monto WHERE sald_caja_id = :caja_id AND sald_cuen_id = :cuen_id', ['caja_id' => $caja_id, 'cuen_id' => $movimiento->getCuenta()->getId(), 'monto' => $movimiento->getMonto()]);
    }
}