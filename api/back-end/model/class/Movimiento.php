<?php
abstract class Movimiento
{
    protected $id;
    protected $monto;
    protected $fecha;
    protected $descripcion;
    protected $usuario;
    protected $cuenta;
    protected $_caja;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getMonto() { return $this->monto; }
    public function setMonto($monto) { $this->monto = $monto; }
    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function getUsuario() { return $this->usuario; }
    public function setUsuario($usuario) { $this->usuario = $usuario; }
    public function getCuenta() { return $this->cuenta; }
    public function setCuenta($cuenta) { $this->cuenta = $cuenta; }
    public function getCaja() { return $this->_caja; }
    public function setCaja($caja) { $this->_caja = $caja; }
}