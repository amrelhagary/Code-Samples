'use strict';


angular
	.module('scrumApp.release', [
		'ngResource'
	])
	.config(['$routeProvider', function($routeProvider) {
		
		$routeProvider
			.when('/release', {
				controller   : 'ReleaseListCtrl',
				templateUrl  : 'release/views/release-list-view.html?' + Date.now()  
			})
			.when('/release/add', {
				controller   : 'ReleaseAddCtrl',
				templateUrl  : 'release/views/release-add-view.html?' + Date.now()  
			})
			.when('/release/edit/:id',{
				controller: 'ReleaseEditCtrl',
				templateUrl: 'release/views/release-edit-view.html?'+ Date.now()
			})
	}])
	.controller('ReleaseAddCtrl', ['$scope','ReleaseFactory','$http','$location','toaster',
		function($scope,ReleaseFactory,$http,$location,toaster){
			$scope.addRelease = function(isValid)
			{
				console.log(isValid)
				// if(isValid)
				// {
					ReleaseFactory.save($scope.release,function(response){
						if(response.status == 'ok')
						{
							$location.path('/release');
							toaster.pop('success',"Add release","release Record Added Successfully");
						}else{
							toaster.pop('error',"Error",response.message);
						}
					});
				// }
			}
  	}])
	.controller('ReleaseListCtrl', ['$scope','ReleaseFactory','$routeParams','toaster',
		function($scope,ReleaseFactory,$routeParams,toaster){
	    	ReleaseFactory.query(function(response){
	    		if(response.status == 'ok')
	    			$scope.releases = response.data;
	    		else
	    			toaster.pop('error',"Error",response.message);
	    	});

	    $scope.deleteRelease = function(releaseId,index){
	    	console.log(releaseId,index)

			ReleaseFactory.delete({id: releaseId},function(response){
				if(response.status == 'ok')
				{
					$scope.releases.splice(index,1);
					toaster.pop('success',"Delete release","release Record Deleted Successfully");
				}
			})
		}
  	}])
	.controller('ReleaseEditCtrl',['$scope','ReleaseFactory','EmployeeFactory','$routeParams','$location','toaster',function($scope,ReleaseFactory,EmployeeFactory,$routeParams,$location,toaster){
		var releaseId = $routeParams.id;
		ReleaseFactory.get({id: releaseId},function(response){
			$scope.release = response.data;
		});

		$scope.updateRelease = function(isValid){
			// update release
			ReleaseFactory.update($scope.release,function(response){
				if(response.status == 'ok')
				{
					$location.path('/release');
					toaster.pop('success',"Update release","release Record Updated Successfully");
				}else{
					toaster.pop('error',"Error release",response.message);
				}
			});
		}
	}])
	.directive('releaseForm',[function(){
		return {
			restrict: 'E',
			templateUrl: 'release/views/_form.html'
		}
	}])
	.directive('scrumMasterList',['ScrumMaster',function(ScrumMaster){
		return {
			restrict: 'E',
			templateUrl: 'release/views/_scrum_master_list.html?'+Date.now(),
			link: function(scope,element,attrs){
				ScrumMaster.get(function(response){
					if(response.status == 'ok')
					{
						if(Array.isArray(response.data) === false)
						{
							scope.employee = new Array();
							scope.employee.push(response.data);
						}
						else
							scope.employee = response.data;
					}
					else
						console.log("Error fetch Scrum Master list")
				})
			}
		}
	}])
