(function(){
    "use strict";

    angular.module('app').service('Dialog', dialog);

    dialog.$inject = [];
    function dialog()
    {
        alertify.defaults.closable = false;
        alertify.defaults.movable = false;
        alertify.defaults.transition = "zoom";
        alertify.defaults.theme.ok = "btn btn-primary pull-right";
        alertify.defaults.theme.cancel = "btn btn-danger pull-left";
        alertify.defaults.theme.input = "form-control";

        var service = {
            wconfirm: wconfirm, 
            ealert: ealert, 
            walert: walert
        };

        return service;

        function wconfirm(message, fn_confirm, fn_cancel)
        {
            alertify.confirm().set('title', '¡Cuidado!').set('message', message).set('onok', fn_confirm).set('oncancel', fn_cancel || angular.noop).set('labels', {ok: 'Si', cancel: 'No'}).showModal('alertify-warning');
        }

        function ealert(message)
        {
            alertify.alert().set('title', '¡Error!').set('message', message).set('label', 'Cerrar').showModal('alertify-error');
        }

        function walert(message)
        {
            alertify.alert().set('title', '¡Error!').set('message', message).set('label', 'Cerrar').showModal('alertify-warning');
        }
    }
})();