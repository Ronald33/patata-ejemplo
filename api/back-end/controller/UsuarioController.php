<?php
class UsuarioController
{
    private $validator;
    private $dao;
    private $view;

    public function __construct()
    {
        $this->validator = Repository::getValidator();
        $this->dao = new UsuarioDAO();
        $this->view = Repository::getResponse();
    }

    public function get($id = null)
    {
        if($id)
        {
            $result = $this->dao->selectById($id);
            if($result) { $this->view->j200($result); }
            else { $this->view->j404(); }
        }
        else if(isset($_GET['user']) && isset($_GET['password']))
		{
			$result = $this->dao->selectByUserAndPassword($_GET['user'], $_GET['password']);
            
            if($result)
            {
                if(!Helper::isUsuarioHabilitado($result)) { $this->view->j401(); }

                if($result instanceof Operador)
                {
                    if(!isset($_GET['terminal_id'])) { $this->view->j401(); }

                    $terminalDao = new TerminalDAO();
                    $terminal = $terminalDao->selectById($_GET['terminal_id']);

                    if(!Helper::isTerminalHabilitado($terminal)) { $this->view->j401(); }

                    $result->setTerminal($terminal);
                }

                $this->view->j200(Helper::getResponseLoginSuccessful($result));
            }
            else { $this->view->j401(); }
		}
        else { $this->view->j200($this->dao->selectAll()); }
    }

    public function post()
    {
        $payload = Helper::getPayload();
        UsuarioHelper::fillValidator($this->validator, $payload);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $usuario = UsuarioHelper::castToUsuario($payload);
        Repository::getDB()->beginTransaction();
        if(!$this->dao->insert($usuario)) { $this->view->j500(); }
        Helper::saveLog($this->dao::$table, 'INSERT', $usuario);
        Repository::getDB()->commit();
        $this->view->j201($usuario);
    }

    public function put($id = null)
    {
        if($id == null) { $this->view->j501(); }
        if(!$this->dao->selectById($id)) { $this->view->j404(); }

        $payload = Helper::getPayload();
        UsuarioHelper::fillValidator($this->validator, $payload, $id);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $usuario = UsuarioHelper::castToUsuario($payload, $id);
        $usuario->setId($id);
        Repository::getDB()->beginTransaction();
        if(!$this->dao->update($usuario)) { $this->view->j500(); }
        Helper::saveLog($this->dao::$table, 'UPDATE', $usuario);
        Repository::getDB()->commit();
        $this->view->j200($usuario);
    }

    public function delete($id = null)
    {
        if($id == null) { $this->view->j501(); }
        if($id == 1) { $this->view->j423(); }
        if(!$this->dao->selectById($id)) { $this->view->j404(); }

        if(!$this->dao->delete($id)) { $this->view->j500(); }
        $this->view->j204();
    }

    public function patch($id = null)
    {
		if($id == NULL) { $this->view->j501(); }
        if($id == 1) { $this->view->s423(); }
        $usuario = $this->dao->selectById($id);
        if(!$usuario) { $this->view->j404(); }

        $payload = Helper::getPayload();
        if($payload)
        {
            if(isset($payload->habilitado))
            {
                if(!in_array($payload->habilitado, [true, false], true)) { $this->view->j400(); }
                if($usuario->getHabilitado() == $payload->habilitado) { $this->view->j400(); }
                if(!$this->dao->setHabilitado($id, $payload->habilitado)) { $this->view->j500(); }
                $this->view->j200(['habilitado' => $payload->habilitado]);
            }
        }
        
        $this->view->j501();
    }
}
