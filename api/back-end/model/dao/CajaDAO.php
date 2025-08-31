<?php
class CajaDAO
{
    private static $table = 'cajas';
    private static $pk = 'caja_id';

    public static function getSelectedFields($alias = NULL, $prefix = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return [
            $alias . 'caja_id' => $prefix . 'id',
            'UNIX_TIMESTAMP(' . $alias . 'caja_apertura)' => $prefix . 'apertura',
            'UNIX_TIMESTAMP(' . $alias . 'caja_cierre)' => $prefix . 'cierre',
            $alias . 'caja_apertura_usua_id' => $prefix . 'aperturaUsuario_id',
            $alias . 'caja_cierre_usua_id' => $prefix . 'cierreUsuario_id',
            $alias . 'caja_term_id' => $prefix. 'terminal_id',
        ];
    }

    private static function getFieldsToInsert(Caja $caja)
    {
        $fields = 
        [
            'caja_apertura' => Helper::getDateTimeFromTimestamp($caja->getApertura()), 
            'caja_cierre' => $caja->getCierre() == NULL ? NULL : Helper::getDateTimeFromTimestamp($caja->getCierre()), 
            'caja_apertura_usua_id' => $caja->getAperturaUsuario()->getId(), 
            'caja_cierre_usua_id' => $caja->getCierreUsuario() instanceof Usuario ? $caja->getCierreUsuario()->getId() : null, 
            'caja_term_id' => $caja->getTerminal()->getId(), 
        ];

        return $fields;
    }

    private function processRow(&$row, $key, $params = [])
    {
        $row->id = (int) $row->id;

        $cast = isset($params['cast']) ? $params['cast'] : false;
        $set_sub_items = isset($params['set_sub_items']) ? $params['set_sub_items'] : false;

        if($set_sub_items)
        {
            $aperturaUsuarioDAO = new UsuarioDAO();
            $row->aperturaUsuario = $aperturaUsuarioDAO->selectById($row->aperturaUsuario_id, false);
            
            $terminalDAO = new TerminalDAO();
            $row->terminal = $terminalDAO->selectById($row->terminal_id, false);
            
            if(isset($row->cierreUsuario_id))
            {
                $cierreUsuarioDAO = new UsuarioDAO();
                $row->cierreUsuario = $cierreUsuarioDAO->selectById($row->cierreUsuario_id, false);
            }
            else { $row->cierreUsuario = null; }
            
            $saldoDAO = new SaldoDAO();
            $row->saldos = $saldoDAO->selectByCajaId($row->id, false)['data'];

            unset($row->aperturaUsuario_id); 
            unset($row->cierreUsuario_id); 
            unset($row->terminal_id); 
        }

        if($cast) { $row = CajaHelper::castToCaja($row, $set_sub_items, $row->id); }
    }

    public function selectAll($cast = true, $set_sub_items = true)
    {
        $results = Repository::getDB()->select(self::$table, self::getSelectedFields(), '', [], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast, 'set_sub_items' => $set_sub_items]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function selectById($id, $cast = true, $set_sub_items = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'caja_id = :id', ['id' => $id]);
        if($result) { $this->processRow($result, 0, ['cast' => $cast, 'set_sub_items' => $set_sub_items]); }
        return $result;
    }

    public function selectByTerminalId($term_id, $cast = true, $set_sub_items = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'caja_term_id = :id', ['id' => $term_id]);
        if($result) { $this->processRow($result, 0, ['cast' => $cast, 'set_sub_items' => $set_sub_items]); }
        return $result;
    }

    public function selectByTerminalIdAndBlock($term_id, $cast = true, $set_sub_items = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'caja_term_id = :id', ['id' => $term_id], 'FOR UPDATE');
        if($result) { $this->processRow($result, 0, ['cast' => $cast, 'set_sub_items' => $set_sub_items]); }
        return $result;
    }

    public function insert(Caja $caja)
    {
        $db = Repository::getDB();

        $db->query('SELECT COUNT(*) AS total FROM cajas WHERE caja_term_id = :id AND caja_cierre IS NULL FOR UPDATE', ['id' => $caja->getTerminal()->getId()]);
		if((int) $db->fetch()->total > 0) { return false; }

        $db->insert(self::$table, self::getFieldsToInsert($caja));
        $caja->setId($db->getLastInsertId());
        return true;
    }

    public function cerrar($id, $timestamp, $user)
    {
        $replacements = ['caja_cierre' => Helper::getDateTimeFromTimestamp($timestamp), 'caja_cierre_usua_id' => $user->getId()];
        Repository::getDB()->update(self::$table, $replacements, 'caja_id = :id', ['id' => $id]);
    }
}