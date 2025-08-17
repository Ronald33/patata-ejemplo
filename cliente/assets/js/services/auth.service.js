(function(){
	"use strict";

	angular.module('app').service('Auth', auth);
	auth.$inject = ['CONFIG', 'Helper', '$http', '$localStorage', '$rootScope', '$state', 'Dialog'];

	function auth(CONFIG, Helper, $http, $localStorage, $rootScope, $state, Dialog)
	{
		var service = {
			updateMetadata: updateMetadata, 
			existsTokenInLocalStorage: existsTokenInLocalStorage, 
			login: login, 
			logout: logout
		};

		return service;

		function existsTokenInLocalStorage()
		{
			if($localStorage._token) { return true; }
			else { return false; }
		};

		function updateMetadata()
		{
			$http.defaults.headers.common['patata-authorization'] = $localStorage._token;
			$rootScope._usuario = $localStorage._usuario;
		}

		function login(user, password, terminal)
		{
			var _headers = {'patata-authorization': 'usuario-login'};

			var params = { user: user, password: password };
			if(terminal) { params.terminal_id = terminal.id; }

			$http({
				url: CONFIG.api_url + '/Usuario',
				params: params, 
				method: 'GET',
				headers: _headers
			}).then(function(response){
				$localStorage._token = response.data.token;
				$localStorage._usuario = response.data.user;
				updateMetadata();
				Helper.goToCorrectStage();
			}, function(response){
				if(response.status == 401) { Dialog.ealert('Usuario desconocido'); }
			});
		};

		function logout()
		{
			delete $http.defaults.headers.common['patata-authorization'];
			delete $localStorage._usuario;
			delete $localStorage._token;
			$state.go('login', {}, {reload: true});
		};
	}
})();

