'use strict';

angular
	.module('scrumApp.employee', [
		'ngResource'
	])
	.config(['$routeProvider', function($routeProvider) {
		
		$routeProvider
			.when('/employee', {
				controller   : 'EmployeeListCtrl',
				templateUrl  : 'employee/views/employee-list-view.html?' + Date.now()  
			})
			.when('/employee/add', {
				controller   : 'EmployeeAddCtrl',
				templateUrl  : 'employee/views/employee-add-view.html?' + Date.now()  
			})
			.when('/employee/edit/:id',{
				controller: 'EmployeeEditCtrl',
				templateUrl: 'employee/views/employee-edit-view.html?'+ Date.now()
			})
	}])
	.controller('EmployeeAddCtrl', ['$scope','EmployeeFactory','$http','$location','toaster',
		function($scope,EmployeeFactory,$http,$location,toaster){
			$scope.status = [
				{statusId: 1 , statusDesc: "Active"},
				{statusId: 2, statusDesc: "Suspend" }
			];

			$scope.addEmployee = function(isValid)
			{
				if(isValid)
				{
					console.log($scope.employee)
					EmployeeFactory.save($scope.employee,function(response){
						console.log(response)
						if(response.status == 'ok')
						{
							$location.path('/employee');
							toaster.pop('success',"Add Employee","Employee Record Added Successfully");
						}else{
							toaster.pop('error',"Error",response.message);
						}
					});
				}
			}
  	}])
	.controller('EmployeeListCtrl', ['$scope','EmployeeFactory','$routeParams','toaster',
		function($scope,EmployeeFactory,$routeParams,toaster){
	    	EmployeeFactory.query(function(response){
	    		if(response.status == 'ok')
	    			$scope.employees = response.data;
	    		else
	    			toaster.pop('error',"Error",response.message);
	    	});

	    $scope.deleteEmployee = function(employeeId,index){
	    	console.log(employeeId,index)

			EmployeeFactory.delete({id: employeeId},function(response){
				if(response.status == 'ok')
				{
					$scope.employees.splice(index,1);
					toaster.pop('success',"Delete Employee","Employee Record Deleted Successfully");
				}
			})
		}
  	}])
	.controller('EmployeeEditCtrl',['$scope','EmployeeFactory','$routeParams','$location','toaster',function($scope,EmployeeFactory,$routeParams,$location,toaster){
		var employeeId = $routeParams.id;
		EmployeeFactory.get({id: employeeId},function(response){
			$scope.employee = response.data;
			$scope.status = [
				{statusId: 1 , statusDesc: "Active"},
				{statusId: 2, statusDesc: "Suspend" }
			];
		})

		$scope.updateEmployee = function(){
			console.log('request',$scope.employee)
			EmployeeFactory.update($scope.employee,function(response){
				console.log('response',response);
				$location.path('/employee');
				toaster.pop('success',"Update Employee","Employee Record Updated Successfully");
			})
		}
	}])
	.directive('employeeForm',[function(){
		return {
			restrict: 'E',
			templateUrl: 'employee/views/_form.html'
		}
	}])
	.directive('employeeRole',['EmployeeRole',function(EmployeeRole){
		return {
			restrict: 'E',
			templateUrl: 'employee/views/_employee_role.html',
			link: function(scope,element,attrs){
				EmployeeRole.get(function(response){
					scope.roles = response.data;
				})
			}
		}
	}])
