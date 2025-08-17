(function(){
    "use strict";

    angular.module('app').controller('MenuController', menuController);
    menuController.$inject = ['Auth', 'Dialog'];

    function menuController(Auth, Dialog)
    {
        var vm = this;

        vm.logout = logout;

        function logout()
        {
            Dialog.wconfirm('Se cerrará sesión ¿Desea continuar?' , function(){
                Auth.logout();
            });
        };
    }
})();