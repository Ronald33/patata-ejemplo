(function(){
    "use strict";

    angular.module('app').service('Helper', helper);

    helper.$inject = ['$rootScope', '$state', 'CONFIG', 'Notification', 'Dialog'];
    function helper($rootScope, $state, CONFIG, Notification, Dialog)
    {
        var service = {
            goToCorrectStage: goToCorrectStage, 
            guardar: guardar, 
            guardar_modal: guardar_modal, 
            cambiarAtributo: cambiarAtributo, 
            eliminar: eliminar, 
            updateObject: updateObject, 
            generarPdf: generarPdf, 
            generarXlsx: generarXlsx
        };

        return service;

        function goToCorrectStage()
        {
            if($rootScope._usuario)
            {
                if($rootScope._usuario.__class == 'Administrador') { $state.go(CONFIG.state_initial.administrador); }
                else if($rootScope._usuario.__class == 'Operador') { $state.go(CONFIG.state_initial.operador); }
            }
            else { $state.go('login'); }
        }

        function _guardar($uibModalInstance, resource, item, mensajeExito, estadoExito, mensajeError, vm)
        {
            if(item.id) { resource.update({ id: item.id }, item, guardar_success, guardar_error); }
            else { resource.save(item, guardar_success, guardar_error); }

            function guardar_success(response)
            {
                Notification.success(mensajeExito);
                if($uibModalInstance) { $uibModalInstance.close(response); }
                else { $state.go(estadoExito); }
            }
    
            function guardar_error(response)
            {
                if(response.status === 400) { vm.errors = response.data;  } // Asigna los errores al controlador
                Notification.error(mensajeError);
            }
        }

        function guardar(resource, item, mensajeExito, estadoExito, mensajeError, vm)
        {
            _guardar(null, resource, item, mensajeExito, estadoExito, mensajeError, vm);
        }

        function guardar_modal(resource, item, mensajeExito, mensajeError, vm, $uibModalInstance)
        {
            _guardar($uibModalInstance, resource, item, mensajeExito, '', mensajeError, vm);
        }

        function eliminar(resource, item, lista, mensajeConfirmacion, mensajeExito)
        {
            Dialog.wconfirm(mensajeConfirmacion, function() {
                resource.delete({ id: item.id }, function() {
                    lista.splice(lista.indexOf(item), 1);
                    Notification.success(mensajeExito);
                });
            });
        }

        function cambiarAtributo(resource, item, cambios, mensajeConfirmacion, mensajeExito)
        {
            Dialog.wconfirm(mensajeConfirmacion, function(){
                resource.patch({ id: item.id }, cambios, function(response) {
                    Object.assign(item, response);
                    Notification.success(mensajeExito);
                });
            });
        };

        function updateObject(old_object, new_object)
        {
            for(var key in old_object)
            {
                if(new_object.hasOwnProperty(key)) { old_object[key] = new_object[key]; }
            }
        }

	function generarPdf(title, headers, data, filename)
        {
            var doc = new window.jspdf.jsPDF({orientation: 'landscape', format: 'a4'});

            var pageWidth = doc.internal.pageSize.getWidth();
            var textWidth = doc.getTextWidth(title);

            doc.setFontSize(18);
            doc.setTextColor(200, 0, 0);
            doc.text(title, (pageWidth - textWidth) / 2, 15);

             doc.autoTable({
                head: headers,
                body: data,
                startY: 20
            });

            doc.save(filename + ".pdf");
        }

        function generarXlsx(title, data, filename)
        {
            var worksheet = XLSX.utils.json_to_sheet(data);
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, title);

            var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
            saveAs(new Blob([wbout], { type: 'application/octet-stream' }), filename + '.xlsx');
        }
    }
})();
