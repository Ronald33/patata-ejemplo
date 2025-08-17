(function(){
    "use strict";

    var resources =
    [
        { name: 'ResCaja', target: 'Caja', patch: true }, 
        { name: 'ResCuenta', target: 'Cuenta', patch: false }, 
        { name: 'ResExtras', target: 'Extras', patch: true }, 
        { name: 'ResMovimiento', target: 'Movimiento', patch: false }, 
        { name: 'ResPersona', target: 'Persona', patch: false }, 
        { name: 'ResTerminal', target: 'Terminal', patch: true },
        { name: 'ResUsuario', target: 'Usuario', patch: true }
    ];

    angular.forEach(resources, function(resource)
    {
        var app = angular.module('app');

        app.factory(resource.name, ['CONFIG', '$resource', function(CONFIG, $resource) {
            var extras =  { query: { method: 'GET', isArray: false }, update: { method: 'PUT' } };
            if(resource.patch) { extras.patch = { method: 'PATCH' }; }
            return $resource(CONFIG.api_url + '/' + resource.target + '/:id', {id: '@id'}, extras);
        }]);
    });
})();
