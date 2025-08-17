<?php
class PersonaController
{
    private $validator;
    private $dao;
    private $view;

    public function __construct()
    {
        $this->validator = Repository::getValidator();
        $this->dao = new PersonaDAO();
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
        else if(isset($_GET['needle'])) { $this->view->j200($this->dao->selectByNeedle($_GET['needle'])); }
        else { $this->view->j200($this->dao->selectAll()); }
    }

    public function post()
    {
        $payload = Helper::getPayload();
        PersonaHelper::fillValidator($this->validator, $payload);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $persona = PersonaHelper::castToPersona($payload);
        if(!$this->dao->insert($persona)) { $this->view->j500(); }
        $this->view->j201($persona);
    }

    public function put($id = null)
    {
        if($id == null) { $this->view->j501(); }
        if(!$this->dao->selectById($id)) { $this->view->j404(); }

        $payload = Helper::getPayload();
        PersonaHelper::fillValidator($this->validator, $payload, $id);
        if($this->validator->hasErrors()) { $this->view->j400($this->validator->getInputsWithErrors()); }
        $persona = PersonaHelper::castToPersona($payload, $id);
        $persona->setId($id);
        if(!$this->dao->update($persona)) { $this->view->j500(); }
        $this->view->j200($persona);
    }
}