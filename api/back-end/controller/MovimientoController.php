<?php
class MovimientoController
{
    private $validator;
    private $dao;
    private $view;

    public function __construct()
    {
        $this->validator = Repository::getValidator();
        $this->dao = new MovimientoDAO();
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
        else if(isset($_GET['caja_id']))
        {
            $result = $this->dao->selectByCajaId($_GET['caja_id']);
            $this->view->j200($result);
        }
        else if(isset($_GET['filter']))
        {
            switch($_GET['filter'])
            {
                case 'mode-operador':
                    MovimientoHelper::validatePaginationAndSort($this->validator);
                    if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
                    $user = Helper::getCurrentUser();
                    if($user instanceof Operador) { $this->view->j200($this->dao->selectByCajaId(Helper::getCurrentCaja()->getId())); }
            }
            $this->view->j404();
        }
        else
        {
            MovimientoHelper::validatePaginationAndSort($this->validator);
            if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
            $this->view->j200($this->dao->selectAll());
        }
    }

    public function post()
    {
        $payload = Helper::getPayload();
        MovimientoHelper::fillValidator($this->validator, $payload);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $movimiento = MovimientoHelper::castToMovimiento($payload);
        $user = Helper::getCurrentUser();
        $caja = ($user instanceof Administrador) ? MovimientoHelper::getCaja($payload) : Helper::getCurrentCaja();
        if(!$this->dao->insert($movimiento, $caja->getId())) { $this->view->j500(); }
        $this->view->j201($movimiento);
    }

    public function put($id = null)
    {
        if($id == null) { $this->view->j501(); }
        if(!$this->dao->selectById($id)) { $this->view->j404(); }

        $payload = Helper::getPayload();
        MovimientoHelper::fillValidator($this->validator, $payload, $id);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $movimiento = MovimientoHelper::castToMovimiento($payload, $id);
        $movimiento->setId($id);
        if(!$this->dao->update($movimiento)) { $this->view->j500(); }
        $this->view->j200($movimiento);
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