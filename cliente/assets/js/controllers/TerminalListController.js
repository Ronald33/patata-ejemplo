(function(){
    "use strict";
    angular.module('app').controller('TerminalListController', terminalListaController);

    terminalListaController.$inject = ['ResTerminal', '$uibModal', 'Helper'];
    function terminalListaController(ResTerminal, $uibModal, Helper)
    {
        var vm = this;
        
        vm.abrirForm = abrirForm;
        vm.requestDataTable = requestDataTable;
        vm.eliminar = eliminar;
        vm.deshabilitar = deshabilitar;
        vm.habilitar = habilitar;

        _init();

        function _init()
        {
            requestDataTable();
        }

        function abrirForm(terminal)
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'partials/terminal/form.html', 
                controller: 'TerminalFormController', 
                controllerAs: 'vm', 
                bindToController: true, 
                size: 'lg', 
                resolve: { terminal: function() { return terminal || null; } }
            });

            modalInstance.result.then(function(response){
                if(terminal) { Helper.updateObject(terminal, response); }
                else { vm.terminales.push(response); }
            }, angular.noop);
        }

        function requestDataTable()
        {
            ResTerminal.query().$promise.then(function(response){
                vm.terminales = response.data;
                vm.displayCollection = [].concat(vm.terminales);
            });
        }

        function eliminar(terminal)
        {
            Helper.eliminar(ResTerminal, terminal, vm.terminales, 'Se eliminará el terminal: <b>' + terminal.nombre + '</b>, ¿Desea continuar?', 'La terminal fue eliminada correctamente');
        }

        function _habilitarDeshabilitar(terminal, estado)
        {
            var atributo = { "habilitado": estado };
            var mensajeConfirmacion = 'Se ' + (estado ? 'habilitará' : 'deshabilitará') + ' el terminal: <b>' + terminal.nombre + '</b>, ¿Desea continuar?';
            var mensajeExito = 'El terminal <b>' + terminal.nombre + '</b> fue '+ (estado ? 'habilitado' : 'deshabilitado') + ' correctamente';

            Helper.cambiarAtributo(ResTerminal, terminal, atributo, mensajeConfirmacion, mensajeExito);
        }

        function deshabilitar(terminal) { _habilitarDeshabilitar(terminal, false); }

        function habilitar(terminal) { _habilitarDeshabilitar(terminal, true); }
    }
})();