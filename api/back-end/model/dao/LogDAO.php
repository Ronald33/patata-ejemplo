<?php
class LogDAO
{
    private static $table = 'logs';
    private static $pk = 'log_id';

    public static function getSelectedFields($alias = NULL, $prefix = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return [
            $alias . 'log_id' => $prefix . 'id',
            $alias . 'logs_tabla' => $prefix . 'tabla',
            $alias . 'logs_accion' => $prefix . 'accion',
            $alias . 'logs_registro' => $prefix . 'registro',
            $alias . 'logs_ip' => $prefix . 'ip',
            'UNIX_TIMESTAMP(' . $alias . 'logs_fecha)' => $prefix . 'fecha',
            $alias . 'logs_usua_id' => $prefix . 'usuario_id',
        ];
    }

    private static function getFieldsToInsert(Log $log)
    {
        $fields = 
        [
            'logs_tabla' => $log->getTabla(),
            'logs_accion' => $log->getAccion(),
            'logs_registro' => $log->getRegistro(),
            'logs_ip' => $log->getIp(),
            'logs_fecha' => $log->getFecha() == NULL ? NULL : Helper::getDateTimeFromTimestamp($log->getFecha()), 
            'logs_usua_id' => $log->getUsuario()->getId(), 
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
            
            $usuarioDAO = new UsuarioDAO();
            $row->usuario = $usuarioDAO->selectById($row->usuario_id, false);

            unset($row->usuario_id);
        }

        if($cast) { $row = LogHelper::castToLog($row, $set_sub_items, $row->id); }
    }

    public function selectAll($cast = true, $set_sub_items = true)
    {
        $results = Repository::getDB()->select(self::$table, self::getSelectedFields(), '', [], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast, 'set_sub_items' => $set_sub_items]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function selectById($id, $cast = true, $set_sub_items = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'log_id = :id', ['id' => $id]);
        if($result) { $this->processRow($result, 0, ['cast' => $cast, 'set_sub_items' => $set_sub_items]); }
        return $result;
    }

    public function insert(Log $log)
    {
        $db = Repository::getDB();
        $db->insert(self::$table, self::getFieldsToInsert($log));
        $log->setLogId($db->getLastInsertId());
        return true;
    }
}