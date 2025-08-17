(function(){
    "use strict";

    angular.module('app').controller('TerminalFormController', terminalFormController);

    terminalFormController.$inject = ['$uibModalInstance', 'ResTerminal', 'Helper', 'terminal'];
    function terminalFormController($uibModalInstance, ResTerminal, Helper, terminal)
    {
        var vm = this;

        vm.cerrar = cerrar;
        vm.guardar = guardar;

        _init();

        function _init()
        {
            if(terminal)
            {
                vm.action = 'Editar';
                ResTerminal.get({id: terminal.id}, function(response){
                    vm.terminal = response;
                });
            }
            else
            {
                vm.action = 'Agregar';
                vm.terminal = {};
            }
        }

        function cerrar() { $uibModalInstance.dismiss('cancel'); }

        function guardar()
        {
            var item = angular.copy(vm.terminal);
            Helper.guardar_modal(ResTerminal, item, 'El terminal fue guardado correctamente', 'El terminal no pudo ser guardado', vm, $uibModalInstance);
        }
    }
})();