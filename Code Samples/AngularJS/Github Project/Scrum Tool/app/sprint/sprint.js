'use strict';

angular
	.module('scrumApp.sprint', [
		'ngResource',
		'ngDraggable',
		'googlechart'
	])
	.config(['$routeProvider', function($routeProvider) {
		
		$routeProvider
			.when('/sprint', {
				controller   : 'SprintListCtrl',
				templateUrl  : 'sprint/views/sprint-list-view.html?' + Date.now()  
			})
			.when('/sprint/add', {
				controller   : 'SprintAddCtrl',
				templateUrl  : 'sprint/views/sprint-add-view.html?' + Date.now()  
			})
			.when('/sprint/edit/:id',{
				controller: 'SprintEditCtrl',
				templateUrl: 'sprint/views/sprint-edit-view.html?'+ Date.now()
			})
			.when('/add/userstory/sprint/:id',{
				controller: 'AddUserstoryToSprintCtrl',
				templateUrl: 'sprint/views/add-userstory-sprint.html?'+ Date.now()
			})
			.when('/burndown/:id',{
				controller: 'BurndownCtrl',
				templateUrl: 'sprint/views/burndown-sprint-view.html?'+ Date.now()
			});
	}])
	.value('googleChartApiConfig', {
            version: '1',
            optionalSettings: {
                packages: ['corechart', 'gauge'],
                language: 'en'
            }
    })
	.controller('SprintAddCtrl', ['$scope','SprintFactory','$http','$location','toaster',
		function($scope,SprintFactory,$http,$location,toaster){
			$scope.addSprint = function(isValid)
			{
				if(isValid)
				{
					SprintFactory.save($scope.sprint,function(response){
						console.log(response)
						if(response.status == 'ok')
						{
							$location.path('/sprint');
							toaster.pop('success',"Add Sprint","Sprint Record Added Successfully");
						}else{
							toaster.pop('error',"Error",response.message);
						}
					});
				}
			}
  	}])
	.controller('SprintListCtrl', ['$scope','SprintFactory','$routeParams','toaster',
		function($scope,SprintFactory,$routeParams,toaster){
	    	SprintFactory.query(function(response){
	    		if(response.status == 'ok')
	    			$scope.sprintList = response.data;
	    		else
	    			toaster.pop('error',"Error",response.message);
	    	});

	    $scope.deleteSprint = function(sprintId,index){

			SprintFactory.delete({id: sprintId},function(response){
				if(response.status == 'ok')
				{
					$scope.sprintList.splice(index,1);
					toaster.pop('success',"Delete Sprint","Sprint Record Deleted Successfully");
				}
			})
		}
  	}])
	.controller('SprintEditCtrl',['$scope','SprintFactory','$routeParams','$location','toaster',function($scope,SprintFactory,$routeParams,$location,toaster){
		var sprintId = $routeParams.id;
		SprintFactory.get({id: sprintId},function(response){
			$scope.sprint = response.data;
		});

		$scope.updateSprint = function(){
			SprintFactory.update($scope.sprint,function(response){
				$location.path('/sprint');
				toaster.pop('success',"Update Sprint","Sprint Record Updated Successfully");
			})
		}
	}])
	.controller('AddUserstoryToSprintCtrl',['$scope','UserstoryBySprintId','NonSprintUserstory','UserstoryFactory','$routeParams','SprintFactory',
		function($scope,UserstoryBySprintId,NonSprintUserstory,UserstoryFactory,$routeParams,SprintFactory){

		var sprintId = $routeParams.id;

		$scope.userstoriesAssignedToSprint = [];
		$scope.userstoriesNotAssigned = [];

		//get list of user stories assigned to sprint , by giving sprint id
		UserstoryBySprintId.get({ sprintId: sprintId },function(response){
			$scope.userstoriesAssignedToSprint = response.data;
		});

		//get list of userstories not assigned to sprint
		NonSprintUserstory.get(function(response){
			$scope.userstoriesNotAssigned = response.data;
		});

		SprintFactory.get({id: sprintId},function(response){
			$scope.sprint  = response.data;
		})

		$scope.addUserstoryToSprint = function(userstory,evt){
            var index = $scope.userstoriesAssignedToSprint.indexOf(userstory);

            if (index == -1)
            {

            	// update index in dropped area
            	$scope.userstoriesAssignedToSprint.push(userstory);
            	userstory.sprint = $scope.sprint;


            	// remove index from dragged area
            	var index = $scope.userstoriesNotAssigned.indexOf(userstory);
            	if (index > -1) {
            		$scope.userstoriesNotAssigned.splice(index, 1);
            	}

            	UserstoryFactory.update(userstory,function(response){
            		console.log(response)
            	})

            }
        }

        $scope.removeUserstoryFromSprint = function(userstory,evt){
            var index = $scope.userstoriesNotAssigned.indexOf(userstory);

            if (index > -1) {
                $scope.userstoriesNotAssigned.splice(index, 1);


                $scope.userstoriesNotAssigned.push(userstory);
                userstory.sprint = {};
            	
            	var index = $scope.userstoriesAssignedToSprint.indexOf(userstory);
            	if (index > -1) {
            		$scope.userstoriesAssignedToSprint.splice(index, 1);
            	}

                UserstoryFactory.update(userstory,function(response){
                	console.log(response)
                });
            }
        }

        // $scope.onDragSuccess=function(userstory,evt){

        //     var index = $scope.userstoriesNotAssigned.indexOf(userstory);
        //     console.log(index)
        //     if (index > -1) {
        //         $scope.userstoriesNotAssigned.splice(index, 1);
        //     }
        // }

	}])
	.controller('BurndownCtrl', ['$scope','SprintFactory','ProgressRecordFactory','$routeParams',
		function($scope,SprintFactory,ProgressRecordFactory,$routeParams){
		var sprintId = $routeParams.id;

		var sprint = SprintFactory.get({id: sprintId});

		sprint.$promise.then(function(response){
			$scope.sprint = response.data;


			ProgressRecordFactory.get({id: sprintId},function(response){
			if(response.status == 'ok'){
				var dummyData = [
					{"dateObj":{"day":24,"month":9,"year":2015},"actualEffort":3.0,"estimatedEffort":3.0},
					{"dateObj":{"day":25,"month":9,"year":2015},"actualEffort":2.5,"estimatedEffort":2.0},
					{"dateObj":{"day":26,"month":9,"year":2015},"actualEffort":.25,"estimatedEffort":1.0},
					{"dateObj":{"day":27,"month":9,"year":2015},"actualEffort":0.1,"estimatedEffort":0.0},
				]
				// $scope.drawChart(response.data);
				$scope.drawChart(dummyData)
			};
		});

		});

		

		$scope.drawChart = function(data)
		{
			$scope.chartObject = {};
			var rows = [];
			for(var i = 0;i < data.length;i++)
			{
				var dateObj = data[i].dateObj;
				var dateType = new Date(dateObj.year,dateObj.month,dateObj.day);
				var row = {
					c : [
						{ v: dateType },
						{ v: data[i].actualEffort },
						{ v: data[i].estimatedEffort }
					]
				};
				rows.push(row);
			}
			$scope.chartObject.data = {
				"cols": [
					{ id: "m",label: "Month",type: "date" },
					{ id: "a",label: "Actual Effort",type: "number"},
					{ id: "e",label: "Estimated Effort",type: "number" }
				],"rows": rows
			};

			$scope.chartObject.type = 'LineChart';
			$scope.chartObject.options = { 
				'title': 'Burn down chart for Sprint: ' + $scope.sprint.sprName,
				series: {
		          0: { axis: 'a' },
		          1: { axis: 'e',lineWidth: 3,lineDashStyle: [7, 2, 4, 3]}
		      }
			}
		};
	}])
	.directive('sprintForm',[function(){
		return {
			restrict: 'E',
			templateUrl: 'sprint/views/_form.html'
		}
	}]);
