<?php
class Log implements JsonSerializable
{
    private $logId;
    private $tabla;
    private $accion;
    private $registro;
    private $ip;
    private $fecha;
    private $usuario;

    public function getLogId() { return $this->logId; }
    public function setLogId($logId) { $this->logId = $logId; }
    public function getTabla() { return $this->tabla; }
    public function setTabla($tabla) { $this->tabla = $tabla; }
    public function getAccion() { return $this->accion; }
    public function setAccion($accion) { $this->accion = $accion; }
    public function getRegistro() { return $this->registro; }
    public function setRegistro($registro) { $this->registro = $registro; }
    public function getIp() { return $this->ip; }
    public function setIp($ip) { $this->ip = $ip; }
    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function getUsuario() { return $this->usuario; }
    public function setUsuario($usuario) { $this->usuario = $usuario; }

    public function jsonSerialize(): array { return array_merge(get_object_vars($this), ['__class' => get_class($this)]); }
}