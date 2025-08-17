<?php
class PersonaDAO
{
    private static $table = 'personas';
    private static $pk = 'pers_id';

    public static function getSelectedFields($alias = NULL, $prefix = '')
    {
        $alias = $alias ? $alias . '.' : '';

        return [
            $alias . 'pers_id' => $prefix . 'id',
            $alias . 'pers_nombres' => $prefix . 'nombres',
            $alias . 'pers_apellidos' => $prefix . 'apellidos',
            $alias . 'pers_documento' => $prefix . 'documento',
            $alias . 'pers_email' => $prefix . 'email',
            $alias . 'pers_telefono' => $prefix . 'telefono',
            $alias . 'pers_direccion' => $prefix . 'direccion',
        ];
    }

    private static function getFieldsToInsert(Persona $persona)
    {
        $fields = 
        [
            'pers_nombres' => $persona->getNombres(),
            'pers_apellidos' => $persona->getApellidos(),
            'pers_documento' => $persona->getDocumento(),
            'pers_email' => $persona->getEmail(),
            'pers_telefono' => $persona->getTelefono(),
            'pers_direccion' => $persona->getDireccion(),
        ];

        return $fields;
    }

    private function processRow(&$row, $key, $params = [])
    {
        $row->id = (int) $row->id;

        $cast = isset($params['cast']) ? $params['cast'] : false;

        if($cast) { $row = PersonaHelper::castToPersona($row, $row->id); }
    }

    public function selectAll($cast = true)
    {
        $results = Repository::getDB()->select(self::$table, self::getSelectedFields(), '', [], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function selectByNeedle($needle, $cast = true)
    {
        $results = Repository::getDB()->select(self::$table, self::getSelectedFields(), 'pers_nombres LIKE :needle OR pers_apellidos LIKE :needle OR pers_documento LIKE :needle', ['needle' => '%' . $needle . '%'], 'ORDER BY ' . self::$pk . ' DESC');
        array_walk($results, [$this, 'processRow'], ['cast' => $cast]);
        return ['data' => $results, 'total' => sizeof($results)];
    }

    public function selectById($id, $cast = true)
    {
        $result = Repository::getDB()->selectOne(self::$table, self::getSelectedFields(), 'pers_id = :id', ['id' => $id]);
        if($result) { $this->processRow($result, 0, ['cast' => $cast]); }
        return $result;
    }

    public function insert(Persona $persona)
    {
        $db = Repository::getDB();
        $db->insert(self::$table, self::getFieldsToInsert($persona));
        $persona->setId($db->getLastInsertId());
        return true;
    }

    public function update(Persona $persona)
    {
        return Repository::getDB()->update(self::$table, self::getFieldsToInsert($persona), 'pers_id = :id', ['id' => $persona->getId()]);
    }
}