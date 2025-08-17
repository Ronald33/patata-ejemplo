(function(){
    "use strict";

    angular.module('app').directive('ipersona', ipersona);

    ipersona.$inject = [];
    function ipersona()
    {
        var directive = {
            restrict: 'E', 
            templateUrl: 'directives/ipersona/html/ipersona.html', 
            scope: {
                isRequired: '@?', 
                autofocus: '@?', 
                persona: '='
            }, 
            controller: ipersonaController, 
            controllerAs: 'vm', 
            bindToController: true
        };

        return directive;
    }

    ipersonaController.$inject = ['$scope', 'ResPersona', '$uibModal', '$timeout'];
    function ipersonaController($scope, ResPersona, $uibModal, $timeout)
    {
        var vm = this;

        vm.$onInit = function()
        {
            if(vm.autofocus == 'true') { $timeout(function () { $scope.$broadcast('ipersona'); }, 20); }
        };

        vm.detalles = detalles;
        vm.abrirForm = abrirForm;
        vm.refresh = refresh;

        function detalles(persona)
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'directives/ipersona/html/partials/details.html', 
                controller: personaDetallesController, 
                controllerAs: 'vm', 
                bindToController: true, 
                resolve: {
                    persona: function() { return persona; }
                }
            });

            modalInstance.result.then(angular.noop, angular.noop);
        }

        function abrirForm(persona)
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'directives/ipersona/html/partials/form.html', 
                controller: personaFormController, 
                controllerAs: 'vm', 
                bindToController: true, 
                resolve: 
                {
                    persona: function() { return persona || null; }, 
                    filter: function() { return persona ? null : vm.filter; }
                }
            });

            modalInstance.result.then(function(persona){
                vm.persona = persona;
                vm.filter = '';
            }, angular.noop);
        }

        function refresh(filter)
        {
            vm.filter = filter;
            ResPersona.query({needle: filter}).$promise.then(function(response) {
                if(response.data) { vm.personas = response.data; }
            });
        }

        function personaDetallesController($uibModalInstance, ResPersona, persona)
        {
            var vm = this;

            vm.cerrar = cerrar;

            _init()

            function _init()
            {
                ResPersona.get({id: persona.id}).$promise.then(function(response){
                    vm.persona = response;
                });
            }
            
            function cerrar() { $uibModalInstance.dismiss('cancel'); }
        }

        function personaFormController($uibModalInstance, ResPersona, Helper, filter, persona)
        {
            var vm = this;

            vm.cerrar = cerrar;
            vm.guardar = guardar;

            _init();

            function _init()
            {
                if(persona)
                {
                    vm.action = 'Editar';
                    ResPersona.get({id: persona.id}, function(response){
                        vm.persona = response;
                    });
                }
                else
                {
                    vm.action = 'Agregar';
                    vm.persona = {};
                    if(/^[0-9]+$/.test(filter)) { vm.persona.documento = angular.copy(filter); }
                }
            }

            function cerrar() { $uibModalInstance.dismiss('cancel'); }
            function guardar()
            {
                var item = angular.copy(vm.persona);
                Helper.guardar_modal(ResPersona, item, 'El persona fue guardado correctamente', 'El persona no pudo ser guardado', vm, $uibModalInstance);
            }
        }
    }
})();