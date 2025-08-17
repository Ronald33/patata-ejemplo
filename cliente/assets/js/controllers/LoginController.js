(function(){
    "use strict";

    angular.module('app').controller('LoginController', loginController);
    
    loginController.$inject = ['Auth', 'ResCaja'];
    function loginController(Auth, ResCaja)
    {
        var vm = this;
        // vm.terminal = { id: 1 };

        vm.login = login;

        _init();

        function _init()
        {
            ResCaja.query({filter: 'abiertas'}).$promise.then(function(response){
                vm.cajas = response.data;
            });
        }
        
        function login() { Auth.login(vm.user, vm.password, vm.terminal); };
    };
})();