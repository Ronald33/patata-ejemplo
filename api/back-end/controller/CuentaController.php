<?php
class CuentaController
{
    private $validator;
    private $dao;
    private $view;

    public function __construct()
    {
        $this->validator = Repository::getValidator();
        $this->dao = new CuentaDAO();
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
        else if(isset($_GET['filter']))
        {
            switch($_GET['filter'])
            {
                case 'caja':
                    $user = Helper::getCurrentUser();
                    if($user instanceof Operador) { $this->view->j200($this->dao->selectByCajaId(Helper::getCurrentCaja()->getId())); }
                    else
                    {
                        if(isset($_GET['caja_id']) && Helper::isPositiveInteger($_GET['caja_id'])) { $this->view->j200($this->dao->selectByCajaId($_GET['caja_id'])); }
                    }
            }
            $this->view->j404();
        }
        else { $this->view->j200($this->dao->selectAll()); }
    }

    public function post()
    {
        $payload = Helper::getPayload();
        CuentaHelper::fillValidator($this->validator, $payload);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $cuenta = CuentaHelper::castToCuenta($payload);
        if(!$this->dao->insert($cuenta)) { $this->view->j500(); }
        $this->view->j201($cuenta);
    }

    public function put($id = null)
    {
        if($id == null) { $this->view->j501(); }
        if($id == 1) { $this->view->j423(); }
        if(!$this->dao->selectById($id)) { $this->view->j404(); }

        $payload = Helper::getPayload();
        CuentaHelper::fillValidator($this->validator, $payload, $id);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $cuenta = CuentaHelper::castToCuenta($payload, $id);
        $cuenta->setId($id);
        if(!$this->dao->update($cuenta)) { $this->view->j500(); }
        $this->view->j200($cuenta);
    }

    public function delete($id = null)
    {
        if($id == null) { $this->view->j501(); }
        if($id == 1) { $this->view->j423(); }
        if(!$this->dao->selectById($id)) { $this->view->j404(); }

        if(!$this->dao->delete($id)) { $this->view->j500(); }
        $this->view->j204();
    }
}