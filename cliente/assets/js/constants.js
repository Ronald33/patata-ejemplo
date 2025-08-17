(function(){
    "use strict";

    angular.module('app').constant('CONFIG', {
        api_url: 'http://localhost/api',
        add_patata_rest_method: false, 
        state_initial: 
        {
            'administrador': 'admin.usuario_lista', 
            'operador': 'admin.movimiento_lista'
        }
    });
})();