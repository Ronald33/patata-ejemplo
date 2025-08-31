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
        $terminal = ($user instanceof Administrador) ? Helper::cast('Terminal', $payload->terminal) : $user->getTerminal();
        Repository::getDB()->beginTransaction();
        $cajaDAO = new CajaDAO();
        $caja = $cajaDAO->selectByTerminalIdAndBlock($terminal->getId(), true, false);
        if($caja->getCierre() != NULL) { Repository::getDB()->rollback(); $this->view->j500(); }
        $saldoDAO = new SaldoDAO();
        $saldo = $saldoDAO->selectByCuentaIdAndBlock($caja->getId(), $movimiento->getCuenta()->getId());
        $monto = $movimiento->getMonto();
        $success = $this->dao->insert($movimiento, $caja->getId());
        $actual = $saldo->getActual();
        $actual += $movimiento instanceof Ingreso ? $monto : -$monto;
        $saldo->setActual($actual);
        $success = $success && $saldoDAO->update($saldo);
        if(!$success) { Repository::getDB()->rollback(); $this->view->j500(); }
        Repository::getDB()->commit();
        $this->view->j201($movimiento);
    }
}