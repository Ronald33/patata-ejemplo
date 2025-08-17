<?php
abstract class PersonaHelper
{
    public static function castToPersona($object, $id = NULL)
    {
        $persona = Helper::cast('Persona', $object);


        if($id === NULL)
        {
            // Set values by default
        }
        else
        {

        }

        return $persona;
    }

    public static function fillValidator($validator, $data, $id = NULL)
    {
        $validator->addInputFromObject('Nombres', $data, 'nombres')->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 128);
        $validator->addInputFromObject('Apellidos', $data, 'apellidos')->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 128);
        $validator->addInputFromObject('Documento', $data, 'documento')->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 16)->addRule('isUnique', 'personas', 'pers_documento', isset($id) ? 'pers_id != ' . $id : '1');
        $validator->addInputFromObject('Email', $data, 'email', true)->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 64);
        $validator->addInputFromObject('Telefono', $data, 'telefono', true)->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 16);
        $validator->addInputFromObject('Direccion', $data, 'direccion', true)->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 128);


        if($id != NULL) // For edit cases
        {

        }
    }
}