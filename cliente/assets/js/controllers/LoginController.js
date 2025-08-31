(function(){
    "use strict";

    angular.module('app').controller('LoginController', loginController);
    
    loginController.$inject = ['Auth', 'ResTerminal'];
    function loginController(Auth, ResTerminal)
    {
        var vm = this;
        // vm.terminal = { id: 1 };

        vm.login = login;

        _init();

        function _init()
        {
            ResTerminal.query({filter: 'abiertas'}).$promise.then(function(response){
                vm.terminales = response.data;
            });
        }
        
        function login() { Auth.login(vm.user, vm.password, vm.terminal); };
    };
})();