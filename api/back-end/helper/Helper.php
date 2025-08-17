<?php
abstract class Helper extends PatataHelper
{
	/*public static function myFunction()
	{

	}*/

	public static function isUsuarioIdHabilitado($usuario_id)
    {
        $usuarioDao = new UsuarioDAO();
        $usuario = $usuarioDao->selectById($usuario_id);
        return self::isUsuarioHabilitado($usuario);
    }

	public static function isUsuarioHabilitado(Usuario $usuario) { return $usuario != NULL && $usuario->getHabilitado(); }

	public static function getResponseLoginSuccessful($object, $apply_numeric_check = false)
	{
		$transformed = json_encode($object, $apply_numeric_check ? JSON_NUMERIC_CHECK : 0);
		return ['user' => $object, 'token' => Repository::getREST()->encode(['serialized' => $transformed])];
	}

	public static function isTerminalIdHabilitado($terminal_id)
    {
        $terminalDAO = new TerminalDAO();
        $terminal = $terminalDAO->selectById($terminal_id);
        return self::isTerminalHabilitado($terminal);
    }

	public static function isTerminalHabilitado(Terminal $terminal)
	{
		if($terminal != NULL && $terminal->getHabilitado())
		{
			return true;
		}
		else { return false; }
	}

	public static function getCurrentUser()
	{
		$object = json_decode(Repository::getREST()->getData()['serialized']);

		$usuario = Helper::cast($object->__class, $object);

		$persona = Helper::cast($object->persona->__class, $object->persona);
		$usuario->setPersona($persona);

		if(isset($object->terminal))
		{
			$terminal = Helper::cast($object->terminal->__class, $object->terminal);
			$usuario->setTerminal($terminal);
		}

		return $usuario;
	}

	public static function getCurrentCaja()
	{
		$user = self::getCurrentUser();
		assert($user instanceof Operador);
		$cajaDAO = new CajaDAO();
		return $cajaDAO->selectByTerminalId($user->getTerminal()->getId());
	}

	public static function validatePagination($validator)
    {
        $validator->addInputFromArray('Número de registros', $_GET, 'per_page')->addRule('isPositive');
        $validator->addInputFromArray('Página', $_GET, 'page')->addRule('isPositiveOrZero');
        $validator->addInputFromArray('Reverse', $_GET, 'reverse')->addRule('isBoolean');
    }

	public static function getLimit()
	{
		$page = ((int) $_GET['page']) - 1;
		$offset =  $page * (int) $_GET['per_page'];
		return 'LIMIT ' . $_GET['per_page'] . ' OFFSET ' . $offset;
	}

	public static function getGroupedEntities($row, $separator)
    {
        $results = [];

        foreach($row as $key => $value)
        {
            $parts = explode($separator, $key, 2);

            [$prefix, $field] = $parts;

            if(!isset($results[$prefix])) { $results[$prefix] = new stdClass(); }

            $results[$prefix]->$field = $value;
        }

        return $results;
    }

	public static function saveLog($table, $method, $data)
	{
		$log = new Log();
		$log->setTabla($table);
		$log->setAccion(substr($method, strrpos($method, '::') + 2));
		$log->setRegistro(json_encode($data));
		$log->setFecha(Helper::getCurrentTimestamp());
		$log->setUsuario(Helper::getCurrentUser());
		if(isset($_SERVER['REMOTE_ADDR'])) { $log->setIp($_SERVER['REMOTE_ADDR']); }

		$logDao = new LogDAO();
		$logDao->insert($log);
	}
}
