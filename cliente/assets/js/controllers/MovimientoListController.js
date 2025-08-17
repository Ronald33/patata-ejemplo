(function(){
    "use strict";
    angular.module('app').controller('MovimientoListController', movimientoListaController);

    movimientoListaController.$inject = ['ResMovimiento', '$uibModal', 'Helper', '$rootScope', 'ResExtras', 'ResCuenta', 'ResTerminal', '$filter'];
    function movimientoListaController(ResMovimiento, $uibModal, Helper, $rootScope, ResExtras, ResCuenta, ResTerminal, $filter)
    {
        var vm = this;

        vm.fecha = {};
        vm.showTerminal = null;
        
        vm.abrirForm = abrirForm;
        vm.requestDataTable = requestDataTable;

        var cuentaParams = {};
        var movimientoParams = {};

        vm.lastState = null;

        _init();

        function _init()
        {
            if($rootScope._usuario.__class == 'Operador')
            {
                vm.showTerminal = false;
                cuentaParams.filter = 'caja';
                movimientoParams.filter = 'mode-operador';
            }
            else
            {
                vm.showTerminal = true;

                ResTerminal.query().$promise.then(function(response){
                    vm.terminales = response.data;
                });
            }

            ResExtras.query({id: 'tipos-de-movimiento'}).$promise.then(function(response){
                vm.tipos = response.data;
            });

            ResCuenta.query(cuentaParams).$promise.then(function(response){
                vm.cuentas = response.data;
            });

            // requestDataTable();
        }

        function abrirForm(movimiento)
        {
            var modalInstance = $uibModal.open({
                templateUrl: 'partials/movimiento/form.html', 
                controller: 'MovimientoFormController', 
                controllerAs: 'vm', 
                bindToController: true, 
                size: 'lg', 
                resolve: { movimiento: function() { return movimiento || null; } }
            });

            modalInstance.result.then(function(response){
                // if(movimiento) { Helper.updateObject(movimiento, response); }
                // else { vm.movimientos.push(response); }
                requestDataTable();
            }, angular.noop);
        }

        // function requestDataTable()
        // {
        //     ResMovimiento.query(movimientoParams).$promise.then(function(response){
        //         vm.movimientos = response.data;
        //         vm.displayCollection = [].concat(vm.movimientos);
        //     });
        // }

        function _getDataQuery(tableState)
        {
            var start = tableState.pagination?.start || 0;
            var number = tableState.pagination?.number;
            var page = Math.floor(start / number) + 1;

            var dataQuery = {
                ...movimientoParams,
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

        /* Reportes */
        vm.generarPdf = generarPdf;
        vm.generarXlsx = generarXlsx;

        function _getDataMovimiento(movimiento, json)
        {
            var data = {
                "ID": movimiento.id, 
                "Monto": $filter("myAmount")(movimiento.monto), 
                "Tipo": movimiento.__class, 
                "Fecha": $filter("myDateTime")(movimiento.fecha), 
                "Cuenta": movimiento.cuenta.nombre, 
                "Usuario": $filter("persona")(movimiento.usuario.persona), 
                "Terminal": movimiento._caja.terminal.nombre, 
                "Descripción": movimiento.descripcion
            };

            if(json) { return data; }

            return [
                data["ID"],
                data["Monto"],
                data["Tipo"],
                data["Fecha"],
                data["Cuenta"],
                data["Usuario"],
                data["Terminal"],
                data["Descripción"]
            ];
        }

        function generarPdf()
        {
            var headers = [["ID", "Monto", "Tipo", "Fecha", "Cuenta", "Usuario", "Terminal", "Descripción"]];

            var dataQuery = _getDataQuery(vm.lastState);
            dataQuery.page = 1;
            dataQuery.per_page = 10000;

            ResMovimiento.query(dataQuery).$promise.then(function(response)
            {
                var data = response.data.map(function(movimiento){
                    return _getDataMovimiento(movimiento, false);
                });

                Helper.generarPdf('Reporte de movimientos', headers, data, 'reporte-de-movimientos');
            });
        }

        function generarXlsx()
        {
            var dataQuery = _getDataQuery(vm.lastState);
            dataQuery.page = 1;
            dataQuery.per_page = 10000;

            ResMovimiento.query(dataQuery).$promise.then(function(response)
            {
                var data = response.data.map(function(movimiento) {
                    return _getDataMovimiento(movimiento, true);
                });

                Helper.generarXlsx("Reporte de movimientos", data, "reporte-de-movimientos");
            });
        }
        /* Fin de Reportes */
    }
})();