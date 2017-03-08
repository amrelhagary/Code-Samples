'use strict';

angular
	.module('scrumApp.product', [
		'ngResource'
	])
	.config(['$routeProvider', function($routeProvider) {
		
		$routeProvider
			.when('/product', {
				controller   : 'ProductListCtrl',
				templateUrl  : 'product/views/product-list-view.html?' + Date.now()  
			})
			.when('/product/add', {
				controller   : 'ProductAddCtrl',
				templateUrl  : 'product/views/product-add-view.html?' + Date.now()  
			})
			.when('/product/edit/:id',{
				controller: 'ProductEditCtrl',
				templateUrl: 'product/views/product-edit-view.html?'+ Date.now()
			})
	}])
	.controller('ProductAddCtrl', ['$scope','ProductFactory','$http','$location','toaster',
		function($scope,ProductFactory,$http,$location,toaster){
			$scope.addProduct = function(isValid)
			{
				if(isValid)
				{
					ProductFactory.save($scope.product,function(response){
						console.log(response)
						if(response.status == 'ok')
						{
							$location.path('/product');
							toaster.pop('success',"Add Product","Product Record Added Successfully");
						}else{
							toaster.pop('error',"Error",response.message);
						}
					});
				}
			}
  	}])
	.controller('ProductListCtrl', ['$scope','ProductFactory','$routeParams','toaster',
		function($scope,ProductFactory,$routeParams,toaster){
	    	ProductFactory.query(function(response){
	    		if(response.status == 'ok')
	    			$scope.products = response.data;
	    		else
	    			toaster.pop('error',"Error",response.message);
	    	});

	    $scope.deleteProduct = function(productId,index){
	    	console.log(productId,index)

			ProductFactory.delete({id: productId},function(response){
				if(response.status == 'ok')
				{
					$scope.products.splice(index,1);
					toaster.pop('success',"Delete Product","Product Record Deleted Successfully");
				}
			})
		}
  	}])
	.controller('ProductEditCtrl',['$scope','ProductFactory','$routeParams','$location','toaster',function($scope,ProductFactory,$routeParams,$location,toaster){
		var productId = $routeParams.id;
		ProductFactory.get({id: productId},function(response){
			$scope.product = response.data;
		});

		$scope.updateProduct = function(){
			ProductFactory.update($scope.product,function(response){
				$location.path('/product');
				toaster.pop('success',"Update Product","Product Record Updated Successfully");
			})
		}
	}])
	.directive('productForm',[function(){
		return {
			restrict: 'E',
			templateUrl: 'product/views/_form.html'
		}
	}]);
