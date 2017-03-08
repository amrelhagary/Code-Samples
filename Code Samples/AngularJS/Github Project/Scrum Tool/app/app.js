'use strict';

/* App Module */

var scrumApp = angular.module('scrumApp', [
  'ngRoute',
  'ngResource',
  'ngAnimate',
  'ngCookies',
  'toaster',
  'scrumApp.authentication',
  'scrumApp.employee',
  'scrumApp.product',
  'scrumApp.userstory',
  'scrumApp.release',
  'scrumApp.sprint'
])
.run(['$http','$rootScope', '$location','$cookies',
    function ($http,$rootScope, $location,$cookies) {

        // $rootScope.$on('$routeChangeSuccess',function(event,current,previous){
        //     $rootScope.title = current.$$route.title;
        // });

        // keep user logged in after page refresh
        $rootScope.globals = $cookies.getObject('globals') || {};

        if ($rootScope.globals.currentUser) {
            $http.defaults.headers.common['Authorization'] = 'Basic ' + $rootScope.globals.currentUser.authdata;
        }

        $rootScope.$on('$locationChangeStart', function (event, next, current) {
            //redirect to login page if not logged in
            if ($location.path() !== '/login' && !$rootScope.globals.currentUser) {
                $location.path('/login');
            } 
        });

    }]);