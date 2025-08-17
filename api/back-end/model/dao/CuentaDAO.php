<?php
class CuentaDAO
{
    private static $table = 'cuentas';
    private static $pk = 'cuen_id';

    public static function getSelectedFields($alias = NULL, $prefix = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return [
            $alias . 'cuen_id' => $prefix . 'id',
            $alias . 'cuen_nombre' => $prefix . 'nombre',
        ];
    }

    private static function getFieldsToInsert(Cuenta $cuenta)
    {
        $fields = 
        [
            'cuen_nombre' => $cuenta->getNombre(),
        ];

        return $fields;
    }

    private function processRow(&$row, $key, $params = [])
    {
        $row->id = (int) $row->id;
        
        

        $cast = isset($params['cast']) ? $params['cast'] : false;

        if($cast) { $row = CuentaHelper::castToCuenta($row, $row->id); }
    }

    public function selectAll($cast = true)
    {
        $results = Repository::getDB()->select(self::$table, self::getSelectedFields(), '', [], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function selectByCajaId($caja_id, $cast = true)
    {
        $results = Repository::getDB()->select(self::$table . ' JOIN saldos ON sald_cuen_id = cuen_id', self::getSelectedfields(), 'sald_caja_id = :caja_id', ['caja_id' => $caja_id], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function selectById($id, $cast = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'cuen_id = :id', ['id' => $id]);
        if($result) { $this->processRow($result, 0, ['cast' => $cast]); }
        return $result;
    }

    public function insert(Cuenta $cuenta)
    {
        $db = Repository::getDB();
        $db->insert(self::$table, self::getFieldsToInsert($cuenta));
        $cuenta->setId($db->getLastInsertId());
        return true;
    }

    public function update(Cuenta $cuenta)
    {
        return Repository::getDB()->update(self::$table, self::getFieldsToInsert($cuenta), 'cuen_id = :id', ['id' => $cuenta->getId()]);
    }

    public function delete($id) { return Repository::getDB()->delete(self::$table, 'cuen_id = :id', ['id' => $id]); }
}