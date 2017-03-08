<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'CityGuide\Controller\Index' => 'CityGuide\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'city-guide-wo-slash' => array(
        		'type'    => 'Segment',
        		'options' => array(
    				// Change this to something specific to your module
    				'route'    => '/:language/cityguide',
    				'defaults' => array(
						// Change this value to reflect the namespace in which
						// the controllers for your module are found
						'__NAMESPACE__' => 'CityGuide\Controller',
						'controller'    => 'Index',
						'action'        => 'index',
						'module'        => 'cityGuide',
    				),
    		    ),
                'may_terminate' => true,
                'child_routes' => array()
            ),
        	
        	'city-guide-old-home' => array(
        			'type'    => 'Segment',
        			'options' => array(
        					// Change this to something specific to your module
        					'route'    => '/cityguide/city_guide.html[/]',
        					'defaults' => array(
        							// Change this value to reflect the namespace in which
        							// the controllers for your module are found
        							'__NAMESPACE__' => 'CityGuide\Controller',
        							'controller'    => 'Index',
        							'action'        => 'oldHome',
        							'module'        => 'cityGuide',
        					),
        			)
        	),
        	'city-guide-old-city-home' => array(
        			'type'    => 'Segment',
        			'options' => array(
        					// Change this to something specific to your module
        					'route'    => '/cityguide/:code/:city[/]',
        					'defaults' => array(
        							// Change this value to reflect the namespace in which
        							// the controllers for your module are found
        							'__NAMESPACE__' => 'CityGuide\Controller',
        							'controller'    => 'Index',
        							'action'        => 'oldCity',
        							'module'        => 'cityGuide',
        					),
        			)
        	),
            'city-guide' => array(
                'type'    => 'Segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/:language/cityguide/',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'CityGuide\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                        'module'        => 'cityGuide',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[:cityId].html',
                            'defaults' => array(
                            ),
                            'constraints' => array(
                                'cityId' => '[^(/)]+'
                            ),
                        ),
                    ),
                    'addQuestion' => array(
                    		'type'    => 'Segment',
                    		'options' => array(
                    				'route'    => 'addQuestion',
                    				'defaults' => array(
                    				    '__NAMESPACE__' => 'CityGuide\Controller',
                    				    'controller'    => 'Index',
                    				    'action'        => 'addQuestion',
                    				),
                    		),
                    ),
                    'aboutCity' => array(
                    		'type'    => 'Segment',
                    		'options' => array(
                    				'route'    => 'about[/:cityId]',
                    				'defaults' => array(
                    						'__NAMESPACE__' => 'CityGuide\Controller',
                    						'controller'    => 'Index',
                    						'action'        => 'about',
                    				),
                    		),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
        		'layout/layout' => __DIR__ . '/../../Application/view/layout/layout.phtml',
        		'layout/home' => __DIR__ . '/../../Application/view/layout/layout-home.phtml',
        		'application/index/index' => __DIR__ . '/../../Application/view/application/index/index.phtml',
        		'error/404' => __DIR__ . '/../../Application/view/error/404.phtml',
        		'error/index' => __DIR__ . '/../../Application/view/error/index.phtml',
        		'paginator-slide' => __DIR__ . '/../../Application/view/layout/paginator.phtml'
        ),
        'template_path_stack' => array(
            'CityGuide' => __DIR__ . '/../view',
        ),
    ),
);
