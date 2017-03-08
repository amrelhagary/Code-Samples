<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Career\Controller\Vacancy' => 'Career\Controller\VacancyController',
            'Career\Controller\CareerHeader' => 'Career\Controller\CareerHeaderController',
            'Career\Controller\Career' => 'Career\Controller\CareerController'
        )
    ),
    'router' => array(
        'routes' => array(
            'list_vacancies' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/career',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Career\Controller',
                        'controller' => 'Vacancy',
                        'action' => 'index',
                        'module' => 'Career',
                        'language' => 'en'
                    )
                ),
            ),
            'add_vacancy' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/vacancy/add',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Career\Controller',
                        'controller' => 'Vacancy',
                        'action' => 'add',
                        'module' => 'Career',
                        'language' => 'en'
                    )
                )
            ),
            'edit_vacancy' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/vacancy/edit[/:id]',
                    'constraints'=> array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Career\Controller',
                        'controller' => 'Vacancy',
                        'action' => 'edit',
                        'module' => 'Career',
                        'language' => 'en'
                    )
                )
            ),
            'delete_vacancy' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/vacancy/delete[/:id]',
                    'constraints'=> array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Career\Controller',
                        'controller' => 'Vacancy',
                        'action' => 'delete',
                        'module' => 'Career',
                        'language' => 'en'
                    )
                )
            ),
            'career_header' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/career_header[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Career\Controller\CareerHeader',
                        'action'     => 'index',
                        'module' => 'Career',
                        'language' => 'en'
                    ),
                ),
            ),
            'cv_career' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/cv[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Career\Controller\Career',
                        'action'     => 'index',
                        'module' => 'Career',
                        'language' => 'en'
                    ),
                ),
            ),
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'admin/layout' => __DIR__ . '/../../Career/view/layout/admin_layout.phtml',
            'admin/layout/header' => __DIR__ . '/../../Career/view/layout/admin_header.phtml',
            'admin/layout/footer' => __DIR__ . '/../../Career/view/layout/admin_footer.phtml',
            'admin/layout/left_panel' => __DIR__ . '/../../Career/view/layout/admin_left_panel.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    )
);
