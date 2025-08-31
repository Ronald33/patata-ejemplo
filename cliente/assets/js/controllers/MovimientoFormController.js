(function(){
    "use strict";

    angular.module('app').controller('MovimientoFormController', movimientoFormController);

    movimientoFormController.$inject = ['$uibModalInstance', 'ResMovimiento', 'Helper', 'ResExtras', 'ResTerminal', 'ResCuenta', '$rootScope'];
    function movimientoFormController($uibModalInstance, ResMovimiento, Helper, ResExtras, ResTerminal, ResCuenta, $rootScope)
    {
        var vm = this;

        vm.cerrar = cerrar;
        vm.guardar = guardar;
        vm.updateCuentas = updateCuentas;

        _init();

        function _init()
        {
            vm.action = 'Agregar';
            vm.movimiento = {};
            vm.movimiento.tipo = 'EGRESO';

            ResExtras.query({id: 'tipos-de-movimiento'}).$promise.then(function(response) {
                vm.tipos_de_movimiento = response.data;
            });

            if($rootScope._usuario.__class == 'Administrador')
            {
                ResTerminal.query({filter: 'abiertas'}).$promise.then(function(response) {
                    vm.terminales = response.data;
                });
            }
            
            updateCuentas();
        }

        function updateCuentas()
        {
            var params = {filter: 'terminal'};
            if($rootScope._usuario.__class == 'Administrador')
            {
                if(vm.terminal && vm.terminal.id) { params.terminal_id = vm.terminal.id; }
                else { vm.cuentas = []; return; }
            }
            
            ResCuenta.query(params).$promise.then(function(response){
                vm.cuentas = response.data;
            });
        }

        function cerrar() { $uibModalInstance.dismiss('cancel'); }
        function guardar()
        {
            var item = angular.copy(vm.movimiento);
            item.monto *= 100;
            if($rootScope._usuario.__class == 'Administrador') { item.terminal = vm.terminal; }
            Helper.guardar_modal(ResMovimiento, item, 'El movimiento fue guardado correctamente', 'El movimiento no pudo ser guardado', vm, $uibModalInstance);
        }
    }
})();