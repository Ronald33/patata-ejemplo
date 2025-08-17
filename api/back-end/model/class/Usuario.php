<?php
abstract class Usuario
{
    protected $id;
    protected $usuario;
    protected $contrasenha;
    protected $habilitado;
    protected $persona;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getUsuario() { return $this->usuario; }
    public function setUsuario($usuario) { $this->usuario = $usuario; }
    public function getContrasenha() { return $this->contrasenha; }
    public function setContrasenha($contrasenha) { $this->contrasenha = md5($contrasenha); }
    public function getHabilitado() { return $this->habilitado; }
    public function setHabilitado($habilitado) { $this->habilitado = $habilitado; }
    public function getPersona() { return $this->persona; }
    public function setPersona($persona) { $this->persona = $persona; }
}