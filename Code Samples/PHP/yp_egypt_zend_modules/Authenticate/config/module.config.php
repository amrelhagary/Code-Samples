<?php
return array(
    'service_manager' => array(
        'aliases' => array(
            'Zend\Authentication\AuthenticationService' => 'AuthService',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Authenticate\Controller\Auth' => 'Authenticate\Controller\AuthController',
            'Authenticate\Controller\Success' => 'Authenticate\Controller\SuccessController',
//        	'Authenticate\Controller\Google' => 'Authenticate\Controller\GoogleController'
        		
        ),
    ),
    'router' => array(
        'routes' => array(
             'login' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Authenticate\Controller',
                        'module'        => 'Authenticate',
                        'controller'    => 'Auth',
                        'action'        => 'login',
                        'language'      => 'en'
                    ),
                ),
            ),
            'logout' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Authenticate\Controller',
                        'module'        => 'Authenticate',
                        'controller'    => 'Auth',
                        'action'        => 'logout',
                        'language'      => 'en'
                    ),
                ),
            ),
            'authenticate' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/Authenticate',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Authenticate\Controller',
                        'controller' => 'Auth',
                        'action' => 'authenticate',
                        'module' => 'Authenticate',
                        'language' => 'en'
                    )
                ),
            ),
//            'googleSignin' => array(
//                'type' => 'Segment',
//                'options' => array(
//                    // Change this to something specific to your module
//                    'route' => '/signin',
//                    'defaults' => array(
//                        // Change this value to reflect the namespace in which
//                        // the controllers for your module are found
//                        '__NAMESPACE__' => 'Authenticate\Controller',
//                        'controller' => 'Google',
//                        'action' => 'signin',
//                        'module' => 'Authenticate'
//                    )
//                ),
//                'may_terminate' => true,
//                'child_routes' => array()
//            ),
//            'oauthCallback' => array(
//                'type' => 'Segment',
//                'options' => array(
//                    // Change this to something specific to your module
//                    'route' => '/oauth2callback',
//                    'defaults' => array(
//                        // Change this value to reflect the namespace in which
//                        // the controllers for your module are found
//                        '__NAMESPACE__' => 'Authenticate\Controller',
//                        'controller' => 'Google',
//                        'action' => 'oauthCallback',
//                        'module' => 'Authenticate'
//                    )
//                ),
//                'may_terminate' => true,
//                'child_routes' => array()
//            ),
//            'googleLogout' => array(
//                'type' => 'Segment',
//                'options' => array(
//                    // Change this to something specific to your module
//                    'route' => '/signout',
//                    'defaults' => array(
//                        // Change this value to reflect the namespace in which
//                        // the controllers for your module are found
//                        '__NAMESPACE__' => 'Authenticate\Controller',
//                        'controller' => 'Google',
//                        'action' => 'logout',
//                        'module' => 'Authenticate'
//                    )
//                ),
//                'may_terminate' => true,
//                'child_routes' => array()
//            ),
        ),
    ),
//    'view_manager' => array(
//        'template_path_stack' => array(
//            'Authenticate' => __DIR__ . '/../view',
//        ),
//    ),
    'view_manager' => array(
        'template_map' => array(
            'admin/login/layout' => __DIR__ . '/../../Authenticate/view/layout/layout.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
);