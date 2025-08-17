<?php
class Operador extends Usuario implements JsonSerializable
{
    private $terminal;

    public function setTerminal($terminal) { $this->terminal = $terminal; }
    public function getTerminal() { return $this->terminal; }

    public function jsonSerialize(): array { return array_merge(get_object_vars($this), ['__class' => get_class($this)]); }
}