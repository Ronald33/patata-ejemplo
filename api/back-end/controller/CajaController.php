<?php
class CajaController
{
    private $validator;
    private $dao;
    private $view;

    public function __construct()
    {
        $this->validator = Repository::getValidator();
        $this->dao = new CajaDAO();
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
                case 'abiertas': $result = $this->dao->selectAbiertas(); break;
                default: $this->view->j404();
            }
            $this->view->j200($result);
        }
        else { $this->view->j200($this->dao->selectAll()); }
    }

    public function post()
    {
        $payload = Helper::getPayload();
        CajaHelper::fillValidator($this->validator, $payload);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $caja = CajaHelper::castToCaja($payload);
        $db = Repository::getDB();
        $db->beginTransaction();
        if(!$this->dao->insert($caja)) { $db->rollback(); $this->view->j500(); }

        $saldoDAO = new SaldoDAO();
        if(!$saldoDAO->insertFromCaja($caja)) { $db->rollback(); $this->view->j500(); }

        $db->commit();
        $this->view->j201($caja);
    }

    public function patch($id = null)
    {
        if($id == null) { $this->view->j501(); }
        $caja = $this->dao->selectById($id);
        if(!$caja) { $this->view->j404(); }

        $payload = Helper::getPayload();
        if($payload)
        {
            if(isset($payload->cerrar) && $payload->cerrar == true)
            {
                if($caja->getCierre() != NULL) { $this->view->j400(); }
                $timestamp = Helper::getCurrentTimestamp();
                $user = Helper::getCurrentUser();
                $this->dao->cerrar($id, $timestamp, $user);
                $this->view->j200(['cierre' => $timestamp, 'cierreUsuario' => $user]);
            }
        }

        $this->view->j501();
    }
}