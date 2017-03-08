'use strict';

angular
	.module('scrumApp.userstory', [
		'ngResource'
	])
	.config(['$routeProvider', function($routeProvider) {
		
		$routeProvider
			.when('/userstory', {
				controller   : 'UserstoryListCtrl',
				templateUrl  : 'userstory/views/userstory-list-view.html?' + Date.now(),
				title : 'List User Stories'  
			})
			.when('/userstory/add', {
				controller   : 'UserstoryAddCtrl',
				templateUrl  : 'userstory/views/userstory-add-view.html?' + Date.now(),
				title: 'Add User Story'  
			})
			.when('/userstory/edit/:id',{
				controller: 'UserstoryEditCtrl',
				templateUrl: 'userstory/views/userstory-edit-view.html?'+ Date.now(),
				title : 'Edit User Story'
			})
			.when('/product/:id/userstory',{
				controller: 'UserstoryListCtrl',
				templateUrl: 'userstory/views/userstory-list-view.html?' + Date.now(),
				title : 'List User Stories'
			})
			.when('/userstory/assign/:id',{
				controller: 'AssignUserstoryCtrl',
				templateUrl: 'userstory/views/userstory-assign-view.html?'+Date.now()
			})
			.when('/userstory/assignee/' , {
				controller : 'UserstoryListByAssigneeCtrl',
				templateUrl : 'userstory/views/userstory-list-view.html?' + Date.now(),
			})

			.when('/userstory/details/:id' , {
				controller : 'UserstoryGetCtrl',
				templateUrl : 'userstory/views/userstory-details-view.html?' + Date.now(),
			})
	}])
	.controller('UserstoryAddCtrl', ['$scope','UserstoryFactory','ProductFactory','$http','$location','$routeParams','toaster',
		function($scope,UserstoryFactory,ProductFactory,$http,$location,$routeParams,toaster){

			$scope.addUserstory = function(isValid)
			{

				if(isValid)
				{
					UserstoryFactory.save($scope.userstory,function(response){
						if(response.status == 'ok')
						{
							$location.path('/userstory');
							toaster.pop('success',"Add Userstory","Userstory Record Added Successfully");
						}else{
							toaster.pop('error',"Error",response.message);
						}
					});
				}
			}
  	}])
  	.controller('UserstoryListByAssigneeCtrl', ['$scope', '$rootScope','GetAllUserStoryByUserId','$location','$routeParams','toaster',
		function($scope ,$rootScope,GetAllUserStoryByUserId,$location,$routeParams,toaster){
				var UserId = $rootScope.globals.currentUser.user.id;

				console.log(UserId);
				GetAllUserStoryByUserId.query({UserId : UserId} , function(response){
	    		if(response.status == 'ok')
	    			$scope.userstories = response.data;
	    		else
	    			toaster.pop('error',"Error",response.message);
	    		

	    		//console.log($scope.userstories);
			})
  	}])


	.controller('UserstoryListCtrl', ['$scope','UserstoryByProductId','UserstoryFactory','$routeParams','toaster',
		function($scope,UserstoryByProductId,UserstoryFactory,$routeParams,toaster){
			var productId = $routeParams.id;
			if(productId)
			{
				$scope.productId = productId;
		    	UserstoryByProductId.query({productId: productId },function(response){
		    		if(response.status == 'ok')
		    			$scope.userstories = response.data;
		    		else
		    			toaster.pop('error',"Error",response.message);
		    	});
			}else{
				UserstoryFactory.query(function(response){
	    		if(response.status == 'ok')
	    			$scope.userstories = response.data;
	    		else
	    			toaster.pop('error',"Error",response.message);
	    		});
			}

	    $scope.deleteUserstory = function(userstoryId,index){
			UserstoryFactory.delete({id: userstoryId},function(response){
				if(response.status == 'ok')
				{
					$scope.userstories.splice(index,1);
					toaster.pop('success',"Delete Userstory","Userstory Record Deleted Successfully");
				}else{
					toaster.pop('error',"Error",response.message);
				}
			})
		}
  	}])
	.controller('UserstoryEditCtrl',['$scope','UserstoryFactory','$routeParams','$location','toaster',function($scope,UserstoryFactory,$routeParams,$location,toaster){
		var userstoryId = $routeParams.id;
		UserstoryFactory.get({id: userstoryId},function(response){
			$scope.userstory = response.data;
		})

		$scope.updateUserstory = function(isValid){
			if(isValid)
			{
				UserstoryFactory.update($scope.userstory,function(response){
					if(response.status == 'ok')
					{
						$location.path('/product/'+response.data.product.id+'/userstory');
						toaster.pop('success',"Update Userstory","Userstory Record Updated Successfully");
					}else{
						toaster.pop('error',"Error",response.message);
					}
				})
			}

		}
	
	}])
	.controller('UserstoryGetCtrl', ['$scope','UserStoryGetById','UserstoryFactory','UpdateProgressFactory','$routeParams','$location','toaster', 
		function($scope,UserStoryGetById,UserstoryFactory,UpdateProgressFactory,$routeParams,$location,toaster){
			
			var userstoryId = $routeParams.id;
			UserStoryGetById.get({id: userstoryId},function(response){
				$scope.userstory = response.data;
			});

			// start : 1, stop : 2
			$scope.updateProgress = function(){		
				var statusFlag = ($scope.userstory.usStatus) === 1 ? 2: 1;
				// $scope.userstory.usStatus = ($scope.userstory.usStatus) === 1 ? 2: 1;
				var obj = {
					userstory: $scope.userstory,
					startStopFlag: statusFlag
				};

				UpdateProgressFactory.save(obj,function(response){
					if(response.status == 'ok')
					{
						toaster.pop('success',"Update Userstory","Userstory Record Updated Successfully");
						$scope.userstory.usStatus = ($scope.userstory.usStatus) === 1 ? 2: 1;
					}else{
						toaster.pop('error',"Error",response.message);
					}
				})
		}
	}])
	.controller('AssignUserstoryCtrl', ['$scope','UserstoryFactory','AssignList','$routeParams','$location','toaster', 
		function($scope,UserstoryFactory,AssignList,$routeParams,$location,toaster){
			
			var userstoryId = $routeParams.id;
			UserstoryFactory.get({id: userstoryId},function(response){
				$scope.userstory = response.data;
			});

			AssignList.get(function(response){
				$scope.assignList = response.data;
			});

			$scope.assignUserstory = function(isValid)
			{
				UserstoryFactory.update($scope.userstory,function(response){
					if(response.status == 'ok')
					{
						$location.path('/userstory');
						toaster.pop('success',"Update Userstory","Userstory Record Updated Successfully");
					}else{
						toaster.pop('error',"Error",response.message);
					}
				})
			}
		
	}])
	.directive('userstoryForm',[function(){
		return {
			restrict: 'E',
			templateUrl: 'userstory/views/_form.html'
		}
	}])
	.directive('userstoryDetailsForm',[function(){
		return {
			restrict: 'E',
			templateUrl: 'userstory/views/_userStoryDetails.html'
		}
	}])
	.directive('employeeList',['EmployeeFactory',function(EmployeeFactory){
		return {
			restrict: 'E',
			templateUrl: 'userstory/views/_employee_list.html',	
			link: function(scope,element,attrs){
				EmployeeFactory.get(function(response){
					scope.employeeList = response.data;
				})
			}
		}
	}])
	.directive('sprintList',['SprintFactory',function(SprintFactory){
		return {
			restrict: 'E',
			templateUrl: 'userstory/views/_sprint_list.html',	
			link: function(scope,element,attrs){
				SprintFactory.get(function(response){
					scope.sprintList = response.data;
				})
			}
		}
	}])
	.directive('releaseList',['ReleaseFactory',function(ReleaseFactory){
		return {
			restrict: 'E',
			templateUrl: 'userstory/views/_release_list.html',	
			link: function(scope,element,attrs){
				ReleaseFactory.get(function(response){
					scope.releaseList = response.data;
				})
			}
		}
	}])
