(function(){
    "use strict";
    angular.module('app').controller('UsuarioListController', usuarioListaController);

    usuarioListaController.$inject = ['ResUsuario', '$uibModal', 'Helper', 'ResExtras'];
    function usuarioListaController(ResUsuario, $uibModal, Helper, ResExtras)
    {
        var vm = this;
        
        vm.requestDataTable = requestDataTable;
        vm.eliminar = eliminar;

        vm.ver = ver;
        vm.habilitar = habilitar;
        vm.deshabilitar = deshabilitar;

        _init();

        function _init()
        {
            requestDataTable();

            ResExtras.query({id: 'tipos-de-usuario'}).$promise.then(function(response){
                vm.tipos = response.data;
            });
        }

        function requestDataTable()
        {
            ResUsuario.query().$promise.then(function(response){
                vm.usuarios = response.data;
                vm.displayCollection = [].concat(vm.usuarios);
            });
        }

        function eliminar(usuario)
        {
            Helper.eliminar(ResUsuario, usuario, vm.usuarios, 'Se eliminará el usuario: <b>' + usuario.nombre + '</b>, ¿Desea continuar?', 'La usuario fue eliminada correctamente');
        }

        function ver(usuario)
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'partials/usuario/details.html', 
                controller: function ($uibModalInstance, ResUsuario, ResExtras, usuario)
                {
                    var vm = this;

                    vm.cerrar = cerrar;

                    _init();

                    function _init()
                    {
                        ResUsuario.get({id: usuario.id}).$promise.then(function(response){
                            vm.usuario = response;
                            vm.persona = vm.usuario.persona;
                            vm.usuario.tipo = response.__class == 'Operador' ? 'OPERADOR' : 'ADMINISTRADOR';
                        });

                        ResExtras.query({id: 'tipos-de-usuario'}).$promise.then(function(response){
                            vm.tipos_de_usuario = response.data;
                        });
                    }
                    
                    function cerrar() { $uibModalInstance.dismiss('cancel'); }
                }, 
                controllerAs: 'vm', 
                resolve: 
                {
                    usuario: function() { return usuario; }
                }
            });
            
            modalInstance.result.then(angular.noop, angular.noop);
        }

        function _habilitarDeshabilitar(usuario, estado)
        {
            var atributo = { "habilitado": estado };
            var mensajeConfirmacion = 'Se ' + (estado ? 'habilitará' : 'deshabilitará') + ' al usuario: <b>' + usuario.id + '</b>, ¿Desea continuar?';
            var mensajeExito = 'El usuario <b>' + usuario.id + '</b> fue '+ (estado ? 'habilitado' : 'deshabilitado') + ' correctamente';

            Helper.cambiarAtributo(ResUsuario, usuario, atributo, mensajeConfirmacion, mensajeExito);
        }

        function deshabilitar(usuario) { _habilitarDeshabilitar(usuario, false); }
        function habilitar(usuario) { _habilitarDeshabilitar(usuario, true); }
    }
})();