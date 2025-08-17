<?php
class Egreso extends Movimiento implements JsonSerializable
{
    public function jsonSerialize(): array { return array_merge(get_object_vars($this), ['__class' => get_class($this)]); }
}