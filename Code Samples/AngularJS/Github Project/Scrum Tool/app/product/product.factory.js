'use strict';

angular
	.module('scrumApp.product')
	.factory('ProductFactory', ['$resource',
		  function($resource){
		    return $resource('http://localhost:8085/MUMScrum/API/ProductControllerWS/product/:id', {}, {
		      'query': { method:'GET',isArray:false },
		      'update': { method: 'PUT' }
		    });
  }]);
