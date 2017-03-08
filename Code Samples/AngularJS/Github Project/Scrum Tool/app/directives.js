scrumApp
	.directive('headerMenu',function(){
		return {
			restrict : 'E',
			scope: {
				currentUser: "="
			},
			replace: true,
			templateUrl: 'partials/header_menu.html'
		}
	})
	.directive('productOwnerMenu',function(){
		return {
			restrict: 'E',
			templateUrl: 'partials/product_owner_side_menu.html'
		};
	})
	.directive('scrumMasterMenu',function(){
		return {
			restrict: 'E',
			templateUrl: 'partials/scrum_master_side_menu.html'
		};
	})
	.directive('hrAdminMenu',function(){
		return {
			restrict: 'E',
			templateUrl: 'partials/hr_admin_side_menu.html'
		};
	})
	.directive('developerMenu',function(){
		return {
			restrict: 'E',
			templateUrl: 'partials/developer_side_menu.html'
		};
	})
	.directive('testerMenu',function(){
		return {
			restrict: 'E',
			templateUrl: 'partials/tester_side_menu.html'
		};
	})
	.directive('productList',['ProductFactory',function(ProductFactory){
		return {
			restrict: 'E',
			scope: {
				myDirectiveVar: "="
			},
			templateUrl: 'partials/_product_list.html?'+Date.now(),
			link: function(scope,element,attrs){
				ProductFactory.get(function(response){
					if(response.status == 'ok')
						scope.products = response.data;
					else
						console.log("Error fetch product list")
				})
			}
		}
	}])