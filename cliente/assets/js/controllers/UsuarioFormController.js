(function(){
    "use strict";

    angular.module('app').controller('UsuarioFormController', usuarioFormController);

    usuarioFormController.$inject = ['$stateParams', 'ResUsuario', 'Helper', 'ResExtras', '$rootScope'];
    function usuarioFormController($stateParams, ResUsuario, Helper, ResExtras, $rootScope)
    {
        var vm = this;

        vm.guardar = guardar;

        _init();

        function _init()
        {
            if($stateParams.id)
            {
                vm.action = 'Editar';
                ResUsuario.get({id: $stateParams.id}, function(response){
                    vm.usuario = response;
                    vm.usuario.tipo = response.__class == 'Operador' ? 'OPERADOR' : 'ADMINISTRADOR';
                    vm.cambiarContrasenha = false;
                });
            }
            else
            {
                vm.action = 'Agregar';
                vm.usuario = {};
                vm.usuario.tipo = 'OPERADOR';
            }

            ResExtras.query({id: 'tipos-de-usuario'}).$promise.then(function(response){
                vm.tipos_de_usuario = response.data;
            });
        }

        function guardar()
        {
            var item = angular.copy(vm.usuario);
            Helper.guardar(ResUsuario, item, 'El usuario fue guardado correctamente', $rootScope._usuario.__class == 'Administrador' ? 'admin.usuario_list' : 'admin.movimiento_list', 'El usuario no pudo ser guardado', vm);
        }
    }
})();
