(function(){
	"use strict";

	angular.module('app').factory('AddPatataRESTMethod', addPatataRESTMethod);

	addPatataRESTMethod.$inject = ['$q'];
	function addPatataRESTMethod($q)
	{
		var service = {
			'request': request
		};

		return service;

		function request(config)
		{
			if(config.method == 'PUT' || config.method == 'DELETE' || config.method == 'PATCH')
			{
				config.params = config.params || {};
				config.params.PATATA_REST_METHOD = config.method;
				config.method = 'POST';
			}

			return config || $q.when(config);
		}
	}
})();

(function(){
	angular.module('app').config(config);

	config.$inject = ['CONFIG', '$httpProvider'];
	function config(CONFIG, $httpProvider)
	{
		if(CONFIG.add_patata_rest_method) { $httpProvider.interceptors.push('AddPatataRESTMethod'); }
	}
})();