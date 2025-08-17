(function(){
    "use strict";

    angular.module('app').controller('CajaFormController', cajaFormController);

    cajaFormController.$inject = ['$uibModalInstance', 'ResCaja', 'ResTerminal', 'ResCuenta', 'Helper'];
    function cajaFormController($uibModalInstance, ResCaja, ResTerminal, ResCuenta, Helper)
    {
        var vm = this;

        vm.cerrar = cerrar;
        vm.guardar = guardar;
        vm.agregarSaldo = agregarSaldo;
        vm.customFilter = customFilter;
        vm.eliminar = eliminar;

        _init();

        function _init()
        {
            vm.caja = {};
            vm.caja.saldos = [];
            initSaldo()

            ResTerminal.query({filter: 'cerradas'}).$promise.then(function(response){
                vm.terminales = response.data;
            });

            ResCuenta.query().$promise.then(function(response){
                vm.cuentas = response.data;
            });
        }

        function cerrar() { $uibModalInstance.dismiss('cancel'); }
        function guardar()
        {
            var item = angular.copy(vm.caja);
            Helper.guardar_modal(ResCaja, item, 'El caja fue guardado correctamente', 'El caja no pudo ser guardado', vm, $uibModalInstance);
        }

        function initSaldo()
        {
            vm._saldo = {}
            vm._saldo.inicial = 0;
        }

        function agregarSaldo()
        {
            var tmp = angular.copy(vm._saldo);
            tmp.inicial *= 100;
            vm.caja.saldos.push(tmp);
            vm.cuentas.find(cuenta => cuenta.id === vm._saldo.cuenta.id).hide = true;
            initSaldo();
        }

        function eliminar(saldo)
        {
            vm.cuentas.find(cuenta => cuenta.id === saldo.cuenta.id).hide = false;
            vm.caja.saldos.splice(vm.caja.saldos.indexOf(saldo), 1);
        }

        function customFilter(item) { return item.hide !== true; }
    }
})();