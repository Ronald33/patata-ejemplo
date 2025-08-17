<?php
abstract class UsuarioHelper
{
    public static function castToUsuario($object, $set_sub_items = true, $id = NULL)
    {
        $className = NULL;
        switch($object->tipo)
        {
            case 'ADMINISTRADOR': $className = 'Administrador'; break;
            case 'OPERADOR': $className = 'Operador'; break;
        }
        assert($className != NULL);

        $usuario = Helper::cast($className, $object);

        if(isset($object->contrasenha)) { $usuario->setContrasenha($object->contrasenha); }

        if($id === NULL)
        {
            $usuario->setHabilitado(true);
        }
        else
        {

        }

        if($set_sub_items)
        {
            $usuario->setPersona(PersonaHelper::castToPersona($object->persona, true, isset($object->persona->id) ? $object->persona->id : NULL));

            if($id === NULL)
            {
                
            }
            else
            {
                
            }
        }

        return $usuario;
    }

    public static function fillValidator($validator, $data, $id = NULL)
    {
        $validator->addInputFromObject('Usuario', $data, 'usuario')->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 16)->addRule('isUnique', 'usuarios', 'usua_usuario', isset($id) ? 'usua_id != ' . $id : '1');
        if($id == NULL)
        {
            $validator->addInputFromObject('Contraseña', $data, 'contrasenha')->addRule('isAlphanumericAndSpaces')->addRule('minLengthIs', 2)->addRule('maxLengthIs', 64);
        }
        
        $user = Helper::getCurrentUser();
        if($user instanceof Administrador)
        {
            $validator->addInputFromObject('Habilitado', $data, 'habilitado');

            $extrasDAO = new ExtrasDAO();
            $tipos = $extrasDAO->getEnumValues('usuarios', 'usua_tipo')['data'];
            $validator->addInputFromObject('Tipo', $data, 'tipo')->addRule('isIn', $tipos);

            $personaId = (isset($data->persona) && isset($data->persona->id)) ? $data->persona->id : NULL;
            $validator->addInput('Persona', $personaId)->addRule(['rowExists', 'Ingrese una persona asociada válida'], 'personas', 'pers_id');
        }

        if($id != NULL) // For edit cases
        {

        }
    }
}