'use strict';

angular
	.module('scrumApp.release')
	.factory('ReleaseFactory', ['$resource',
		  function($resource){
		    return $resource('http://localhost:8085/MUMScrum/API/ReleaseControllerWS/release/:id', {}, {
		      'query': { method:'GET',isArray:false },
		      'update': { method:'PUT' }
		    });
  }]);
