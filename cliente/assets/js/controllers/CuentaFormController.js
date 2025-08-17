(function(){
    "use strict";

    angular.module('app').controller('CuentaFormController', cuentaFormController);

    cuentaFormController.$inject = ['$uibModalInstance', 'ResCuenta', 'Helper', 'cuenta'];
    function cuentaFormController($uibModalInstance, ResCuenta, Helper, cuenta)
    {
        var vm = this;

        vm.cerrar = cerrar;
        vm.guardar = guardar;

        _init();

        function _init()
        {
            if(cuenta)
            {
                vm.action = 'Editar';
                ResCuenta.get({id: cuenta.id}, function(response){
                    vm.cuenta = response;
                });
            }
            else
            {
                vm.action = 'Agregar';
                vm.cuenta = {};
            }
        }

        function cerrar() { $uibModalInstance.dismiss('cancel'); }

        function guardar()
        {
            var item = angular.copy(vm.cuenta);
            Helper.guardar_modal(ResCuenta, item, 'El cuenta fue guardado correctamente', 'El cuenta no pudo ser guardado', vm, $uibModalInstance);
        }
    }
})();