(function(){
    "use strict";

    angular.module('app').controller('LogFormController', logFormController);

    logFormController.$inject = ['$uibModalInstance', 'ResLog', 'Helper', 'log'];
    function logFormController($uibModalInstance, ResLog, Helper, log)
    {
        var vm = this;

        vm.cerrar = cerrar;
        vm.guardar = guardar;

        _init();

        function _init()
        {
            if(log)
            {
                vm.action = 'Editar';
                ResLog.get({id: log.id}, function(response){
                    vm.log = response;
                });
            }
            else
            {
                vm.action = 'Agregar';
                vm.log = {};
            }
        }

        function cerrar() { $uibModalInstance.dismiss('cancel'); }

        function guardar()
        {
            var item = angular.copy(vm.log);
            Helper.guardar_modal(ResLog, item, 'El log fue guardado correctamente', 'El log no pudo ser guardado', vm, $uibModalInstance);
        }
    }
})();