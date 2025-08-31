<?php
require_once(PATH_CORE . DIRECTORY_SEPARATOR . 'middleware'. DIRECTORY_SEPARATOR . 'Middleware.php');

class MyMiddlewareRest implements core\middleware\Middleware
{
    private $uriDecoder;

    public function __construct()
    {
        $this->uriDecoder = Repository::getURIDecoder();
    }

    public function execute()
    {
        if(Repository::getREST()->dataIsDecodable()) { return $this->evaluate(); }
        else { return $this->evaluateSpecialCases(); }
    }

    private function validateUser()
    {
        $user = Helper::getCurrentUser();
        if(!Helper::isUsuarioIdHabilitado($user->getId())) { return false; }
        if($user instanceof Operador)
        {
            $terminal_id = $user->getTerminal()->getId();
            if(!(Helper::isTerminalIdHabilitado($terminal_id)) || Helper::getCurrentCaja() == NULL) { return false; }
        }

        return true;
    }

    private function evaluate()
    {
        if($this->validateUser() == false) { return false; }

        $class = $this->uriDecoder->getClass();
        $method = $this->uriDecoder->getMethod();
        $arguments = $this->uriDecoder->getArguments();

        $user = Helper::getCurrentUser();
        $payload = json_decode(file_get_contents('php://input'));

        if($user instanceof Administrador)
        {
            return true;
        }
        else if($user instanceof Operador)
        {
            if($class == 'Extras') { return true; }
            else if($class == 'Usuario')
            {
                if($method == 'get')
                {
                    if(sizeof($arguments) == 0) { return false; }
                    if($arguments && $user->getId() != $arguments[0]) { return false; } // Si intenta ver los detalles de otro usuario

                    return true;
                }
                else if($method == 'put')
                {
                    if($arguments && $user->getId() != $arguments[0]) { return false; } // Si intenta editar otro usuario
                    if($user->getPersona()->getId() != $payload->persona->id) { return false; } // Si intenta modifica la persona asociada
                    if($payload->tipo == 'ADMINISTRADOR') { return false; } // Si se pone el rol de administrador

                    return true;
                }
            }
            else if($class == 'Terminal')
            {
                if($method == 'get') { return true; }
            }
            else if($class == 'Cuenta')
            {
                if($method == 'get' && isset($_GET['filter']) && $_GET['filter'] == 'terminal') { return true; }
            }
            else if($class == 'Movimiento')
            {
                if($method == 'get')
                {
                    if(sizeof($arguments) > 0) { return false; }
                    if(isset($_GET['filter']) && $_GET['filter'] == 'mode-operador') { return true; }
                    
                    return false;
                }
                if(in_array($method, ['post'])) { return true; }
                return false;
            }
        }

        return false;
    }

    private function evaluateSpecialCases()
    {
        $class = $this->uriDecoder->getClass();
        $method = $this->uriDecoder->getMethod();
        $arguments = $this->uriDecoder->getArguments();
        $data = Repository::getREST()->getData();

        if($data == 'CLASS_EXCEPTIONS') { return true; }
        if($data == 'SPECIAL_TOKENS')
        {
            $rest = Repository::getREST();

            if($rest->getToken() == 'usuario-login')
            {
                if($class == 'Usuario' && $method == 'get' && isset($_GET['user']) && isset($_GET['password'])) { return true; }
            }

            return false;
        }

        if($data == 'SKIP_AUTH')
        {
            // Verify access to specific resources
            return true;
        }

        return false;
    }
}
