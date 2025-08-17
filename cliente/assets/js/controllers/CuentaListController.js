(function(){
    "use strict";
    angular.module('app').controller('CuentaListController', cuentaListaController);

    cuentaListaController.$inject = ['ResCuenta', '$uibModal', 'Helper'];
    function cuentaListaController(ResCuenta, $uibModal, Helper)
    {
        var vm = this;
        
        vm.abrirForm = abrirForm;
        vm.requestDataTable = requestDataTable;
        vm.eliminar = eliminar;

        _init();

        function _init()
        {
            requestDataTable();
        }

        function abrirForm(cuenta)
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'partials/cuenta/form.html', 
                controller: 'CuentaFormController', 
                controllerAs: 'vm', 
                bindToController: true, 
                size: 'lg', 
                resolve: { cuenta: function() { return cuenta || null; } }
            });

            modalInstance.result.then(function(response){
                if(cuenta) { Helper.updateObject(cuenta, response); }
                else { vm.cuentas.push(response); }
            }, angular.noop);
        }

        function requestDataTable()
        {
            ResCuenta.query().$promise.then(function(response){
                vm.cuentas = response.data;
                vm.displayCollection = [].concat(vm.cuentas);
            });
        }

        function eliminar(cuenta)
        {
            Helper.eliminar(ResCuenta, cuenta, vm.cuentas, 'Se eliminará el cuenta: <b>' + cuenta.nombre + '</b>, ¿Desea continuar?', 'La cuenta fue eliminada correctamente');
        }
    }
})();