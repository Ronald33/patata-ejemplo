<?php
class Caja implements JsonSerializable
{
    private $id;
    private $apertura;
    private $cierre;
    private $aperturaUsuario;
    private $cierreUsuario;
    private $terminal;
    private $saldos = [];

    public function __construct($id = NULL)
    {
        $this->id = $id;
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getApertura() { return $this->apertura; }
    public function setApertura($apertura) { $this->apertura = $apertura; }
    public function getCierre() { return $this->cierre; }
    public function setCierre($cierre) { $this->cierre = $cierre; }
    public function getAperturaUsuario() { return $this->aperturaUsuario; }
    public function setAperturaUsuario($aperturaUsuario) { $this->aperturaUsuario = $aperturaUsuario; }
    public function getCierreUsuario() { return $this->cierreUsuario; }
    public function setCierreUsuario($cierreUsuario) { $this->cierreUsuario = $cierreUsuario; }
    public function getTerminal() { return $this->terminal; }
    public function setTerminal($terminal) { $this->terminal = $terminal; }
    public function getSaldos() { return $this->saldos; }
    public function setSaldos($saldos) { $this->saldos = $saldos; }
    public function addSaldo($saldo) { array_push($this->saldos, $saldo); }

    public function jsonSerialize(): array { return array_merge(get_object_vars($this), ['__class' => get_class($this)]); }
}