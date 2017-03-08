'use strict';

angular
.module('scrumApp.authentication',[])
.config(['$routeProvider',function($routeProvider){
	$routeProvider
	.when('/login',{
		controller: 'LoginCtrl',
		templateUrl: 'authentication/login.html?'+ Date.now()
	})
	.when('/logout',{
		controller: 'LogoutCtrl',
		template: " "
	})
}])
.controller('LoginCtrl',['$scope','AuthService','$location',function($scope,AuthService,$location){

	AuthService.ClearCredentials();
	$scope.login = function(isValid){

		if(isValid)
		{
			AuthService.login($scope.employee,function(response){
				if(response.success)
				{
					AuthService.setCredentials(response.employee,response.home_route);
					$location.path(response.home_route);
				}else{
					$scope.message = response.message;
				}
			});
		}

	}
}])
.controller('LogoutCtrl', ['AuthService','$location', function(AuthService,$location){
	console.log('logout')
	AuthService.ClearCredentials();
	$location.path('/');
}]);