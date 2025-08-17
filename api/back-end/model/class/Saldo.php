<?php
class Saldo implements JsonSerializable
{
    private $id;
    private $inicial;
    private $actual;
    private $cuenta;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getInicial() { return $this->inicial; }
    public function setInicial($inicial) { $this->inicial = $inicial; }
    public function getActual() { return $this->actual; }
    public function setActual($actual) { $this->actual = $actual; }
    public function getCuenta() { return $this->cuenta; }
    public function setCuenta($cuenta) { $this->cuenta = $cuenta; }

    public function jsonSerialize(): array { return array_merge(get_object_vars($this), ['__class' => get_class($this)]); }
}