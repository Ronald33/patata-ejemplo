<?php
class Persona implements JsonSerializable
{
    private $id;
    private $nombres;
    private $apellidos;
    private $documento;
    private $email;
    private $telefono;
    private $direccion;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNombres() { return $this->nombres; }
    public function setNombres($nombres) { $this->nombres = $nombres; }
    public function getApellidos() { return $this->apellidos; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    public function getDocumento() { return $this->documento; }
    public function setDocumento($documento) { $this->documento = $documento; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function getDireccion() { return $this->direccion; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }

    public function jsonSerialize(): array { return array_merge(get_object_vars($this), ['__class' => get_class($this)]); }
}