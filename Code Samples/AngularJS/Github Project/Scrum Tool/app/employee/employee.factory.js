'use strict';

angular
	.module('scrumApp.employee')
	.factory('EmployeeFactory', ['$resource',
		  function($resource){
		    return $resource('http://localhost:8085/MUMScrum/API/EmployeeControllerWS/employee/:id', {}, {
		      'query': { method:'GET',isArray:false },
		      'update': { method: 'PUT' }
		    });
  	}])
	.factory('EmployeeRole',['$resource',function($resource){
		return $resource('http://localhost:8085/MUMScrum/API/RoleControllerWS/role',{},{
			'query': {method: 'GET', isArray: false }
		})
	}])
	.factory('ScrumMaster',['$resource',function($resource){
		return $resource('http://localhost:8085/MUMScrum/API/EmployeeControllerWS/employee/scrummaster',{},{
			'query': {method: 'GET', isArray: false }
		})
	}])
	.factory('AssignList', ['$resource', function($resource){
		return $resource('http://localhost:8085/MUMScrum/API/EmployeeControllerWS/employee/assignee', {}, {
			'query': {method: 'GET', isArray: false }
		})
	}])
