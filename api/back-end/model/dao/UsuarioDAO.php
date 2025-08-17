<?php
class UsuarioDAO
{
    private static $table = 'usuarios';
    private static $pk = 'usua_id';

    public static function getSelectedFields($alias = NULL, $prefix = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return [
            $alias . 'usua_id' => $prefix . 'id',
            $alias . 'usua_usuario' => $prefix . 'usuario',
            $alias . 'usua_habilitado' => $prefix . 'habilitado',
            $alias . 'usua_tipo' => $prefix . 'tipo',
            $alias . 'usua_pers_id' => $prefix . 'persona_id',
        ];
    }

    private static function getFieldsToInsert(Usuario $usuario)
    {
        $fields =  ['usua_usuario' => $usuario->getUsuario()];

        if(Helper::getCurrentUser() instanceof Administrador)
        {
            $tipo = NULL;
            switch(get_class($usuario))
            {
                case Administrador::class: $tipo = 'ADMINISTRADOR'; break;
                case Operador::class: $tipo = 'OPERADOR'; break;
            }
            assert($tipo != NULL);

            $fields['usua_habilitado'] = $usuario->getHabilitado();
            $fields['usua_tipo'] = $tipo;
            $fields['usua_pers_id'] = $usuario->getPersona()->getId();
        }

        if($usuario->getContrasenha()) { $fields['usua_contrasenha'] = $usuario->getContrasenha(); }

        return $fields;
    }

    private function processRow(&$row, $key, $params = [])
    {
        $row->id = (int) $row->id;
        
        
        
        
        
        $row->habilitado = (bool) $row->habilitado;
        
        

        $cast = isset($params['cast']) ? $params['cast'] : false;
        $set_sub_items = isset($params['set_sub_items']) ? $params['set_sub_items'] : false;

        if($set_sub_items)
        {
            
            $personaDAO = new PersonaDAO();
            $row->persona = $personaDAO->selectById($row->persona_id, false);
            

            

            unset($row->persona_id); 
        }

        if($cast) { $row = UsuarioHelper::castToUsuario($row, $set_sub_items, $row->id); }
    }

    public function selectAll($cast = true, $set_sub_items = true)
    {
        $results = Repository::getDB()->select(self::$table, self::getSelectedFields(), '', [], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast, 'set_sub_items' => $set_sub_items]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function selectByUserAndPassword($usuario, $contrasenha, $cast = true, $set_sub_items = true)
    {
        $db = Repository::getDB();
        $where = 'usua_usuario = :usuario AND usua_contrasenha = :contrasenha';
        $replacements = ['usuario' => $usuario, 'contrasenha' => md5($contrasenha)];
        $result = $db->selectOne(self::$table, self::getSelectedFields(), $where, $replacements);
        if($result) { $this->processRow($result, 0, ['cast' => $cast, 'set_sub_items' => $set_sub_items]); }
        return $result;
    }

    public function selectById($id, $cast = true, $set_sub_items = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'usua_id = :id', ['id' => $id]);
        if($result) { $this->processRow($result, 0, ['cast' => $cast, 'set_sub_items' => $set_sub_items]); }
        return $result;
    }

    public function insert(Usuario $usuario)
    {
        $db = Repository::getDB();
        $db->insert(self::$table, self::getFieldsToInsert($usuario));
        $usuario->setId($db->getLastInsertId());
        Helper::saveLog(self::$table, __METHOD__, $usuario);
        return true;
    }

    public function update(Usuario $usuario)
    {
        Repository::getDB()->update(self::$table, self::getFieldsToInsert($usuario), 'usua_id = :id', ['id' => $usuario->getId()]);
        Helper::saveLog(self::$table, __METHOD__, $usuario);
        return true;
    }

    public function delete($id) { return Repository::getDB()->delete(self::$table, 'usua_id = :id', ['id' => $id]); }

    public function setHabilitado($id, $habilitado)
    {
        $db = Repository::getDB();
        $replacements = ['usua_habilitado' => $habilitado];
        $db->update(self::$table, $replacements, 'usua_id = :id', ['id' => $id]);
    }
}