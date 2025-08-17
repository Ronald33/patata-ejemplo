(function(){
    "use strict";
    angular.module('app').controller('CajaListController', cajaListaController);

    cajaListaController.$inject = ['ResCaja', '$uibModal', 'Helper', 'ResTerminal'];
    function cajaListaController(ResCaja, $uibModal, Helper, ResTerminal)
    {
        var vm = this;
        vm.apertura = {};
        vm.cierre = {};

        vm.lastState = null;
        
        vm.abrirForm = abrirForm;
        vm.requestDataTable = requestDataTable;
        vm.cerrar = cerrar;
        vm.ver = ver;

        _init();

        function _init()
        {
            requestDataTable();

            ResTerminal.query().$promise.then(function(response){
                vm.terminales = response.data;
            });
        }

        function abrirForm()
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'partials/caja/form.html', 
                controller: 'CajaFormController', 
                controllerAs: 'vm', 
                bindToController: true, 
                size: 'xs'
            });

            modalInstance.result.then(function(response){
                vm.cajas.push(response);
            }, angular.noop);
        }

        function requestDataTable()
        {
            ResCaja.query().$promise.then(function(response){
                vm.cajas = response.data;
                vm.displayCollection = [].concat(vm.cajas);
            });
        }

        function ver(caja)
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'partials/caja/details.html', 
                controller: cajaVerController, 
                controllerAs: 'vm', 
                bindToController: true, 
                size: 'xl', 
                resolve: { caja: function() { return caja; } }
            });

            modalInstance.result.then(angular.noop, angular.noop);
        }

        function cerrar(caja)
        {
            Helper.cambiarAtributo(ResCaja, caja, { cerrar: true }, 'Se cerrará la caja: <b>' + caja.id + '</b>, ¿Desea continuar?', 'La caja fue cerrada correctamente');
        }

        function cajaVerController($uibModalInstance, ResCaja, ResMovimiento, ResExtras, ResCuenta, caja)
        {
            var vm = this;

            vm.showTerminal = false;
            vm.showUser = true;

            vm.cerrar = cerrar;
            vm.requestDataTable = requestDataTable;

            _init()

            function _init()
            {
                vm.fecha = {};

                ResCaja.get({id: caja.id}).$promise.then(function(response){
                    vm.caja = response;
                });

                ResExtras.query({id: 'tipos-de-movimiento'}).$promise.then(function(response){
                    vm.tipos = response.data;
                });

                ResCuenta.query().$promise.then(function(response){
                    vm.cuentas = response.data;
                });

                // requestDataTable();
            }

            // function requestDataTable()
            // {
            //     ResMovimiento.query({caja_id: caja.id}).$promise.then(function(response) {
            //         vm.movimientos = response.data;
            //     });
            // }

            function _getDataQuery(tableState)
            {
                var start = tableState.pagination?.start || 0;
                var number = tableState.pagination?.number;
                var page = Math.floor(start / number) + 1;

                var dataQuery = {
                    caja_id: caja.id, 
                    sort: tableState.sort.predicate,
                    reverse: tableState.sort.reverse,
                    page: page,
                    per_page: number,
                    ...tableState.search?.predicateObject
                };

                if(tableState.search.predicateObject && tableState.search.predicateObject.fecha)
                {
                    if(tableState.search.predicateObject.fecha.startDate) { dataQuery.start = tableState.search.predicateObject.fecha.startDate.unix(); }
                    if(tableState.search.predicateObject.fecha.endDate) { dataQuery.end = tableState.search.predicateObject.fecha.endDate.unix(); }
                    delete dataQuery.fecha;
                }

                return dataQuery;
            }

            function requestDataTable(tableState)
            {
                if(tableState)
                {
                    if(!tableState.sort || !tableState.sort.predicate) { return; }
                    if(angular.equals(tableState, vm.lastState)) { return; }
                    vm.lastState = angular.copy(tableState);
                }
                else{ tableState = vm.lastState; }

                ResMovimiento.query(_getDataQuery(tableState)).$promise.then(function(response)
                {
                    vm.movimientos = response.data;
                    vm.displayCollection = [].concat(vm.movimientos);
                    tableState.pagination.numberOfPages = Math.ceil(response.total / (tableState.pagination.number));
                });
            }
            
            function cerrar() { $uibModalInstance.dismiss('cancel'); }
        }
    }
})();