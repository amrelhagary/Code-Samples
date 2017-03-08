<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'RateCard\Controller\Index'               => 'RateCard\Controller\IndexController',
            'RateCard\Controller\PlatformRest'        => 'RateCard\Controller\PlatformRestController',
            'RateCard\Controller\SubPlatformRest'     => 'RateCard\Controller\SubPlatformRestController',
            'RateCard\Controller\UserRest'            => 'RateCard\Controller\UserRestController',
            'RateCard\Controller\ClientRest'          => 'RateCard\Controller\ClientRestController',
            'RateCard\Controller\DealItemRest'        => 'RateCard\Controller\DealItemRestController',
            'RateCard\Controller\ServiceRest'         => 'RateCard\Controller\ServiceRestController',
            'RateCard\Controller\PlatformServiceRest' => 'RateCard\Controller\PlatformServiceRestController',
            'RateCard\Controller\InvoiceRest'         => 'RateCard\Controller\InvoiceRestController',
            'RateCard\Controller\InvoiceDealsRest'    => 'RateCard\Controller\InvoiceDealsRestController',
            'RateCard\Controller\InvoiceReportRest'   => 'RateCard\Controller\InvoiceReportRestController',
            'RateCard\Controller\InvoiceUserRest'   => 'RateCard\Controller\InvoiceUserRestController'
        )
    ),
    'router' => array(
        'routes' => array(
            'rate_card_demo' => array(
                'type' => 'segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/rate-card[/]',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'RateCard\Controller',
                        'controller' => 'Index',
                        'action' => 'demo',
                        'module' => 'RateCard',
                        'language' => 'en'
                    )
                )
            ),
            'rate_card_admin' => array(
                'type' => 'segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/rate-card/admin',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'RateCard\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                        'module' => 'RateCard',
                        'language' => 'en'
                    )
                )
            ),
            'rate_card_login' => array(
                'type' => 'Segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/rate-card/admin/login',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'RateCard\Controller',
                        'controller' => 'Index',
                        'action' => 'login',
                        'module' => 'RateCard',
                        'language' => 'en'
                    )
                )
            ),
            'rate_card_google_oauth2_callback' => array(
                'type' => 'Segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/rate-card/admin/oauthcallback',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'RateCard\Controller',
                        'controller' => 'Index',
                        'action' => 'oauthCallback',
                        'module' => 'RateCard',
                        'language' => 'en'
                    )
                )
            ),
            'rate_card_logout' => array(
                'type' => 'segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/rate-card/admin/logout',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'RateCard\Controller',
                        'controller' => 'Index',
                        'action' => 'logout',
                        'module' => 'RateCard',
                        'language' => 'en'
                    )
                )
            ),
            'rate_card_platform_rest' => array(
                'type' => 'segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/rc/api/platform[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\PlatformRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'rate_card_subPlatform_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/platform[/:pid]/subplatform[/:id]',
                    'constraints' => array(
                        'pid' => '[0-9]+',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\SubPlatformRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'rate_card_order_platform_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/platform/:id/order/:orderId',
                    'constraints' => array(
                        'id'       => '[0-9]+',
                        'orderId'  => '[0-9]+',
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\PlatformOrderRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'rate_card_service_rest' => array(
                'type' => 'segment',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/rc/api/service[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\ServiceRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'rate_card_user_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/user[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\UserRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'rate_card_client_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/client[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\ClientRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'rate_card_deal_item_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/deal[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\DealItemRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'platform_service_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/platform[/:pid]/service[/:sid]',
                    'constraints' => array(
                        'pid' => '[0-9]+',
                        'sid' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\PlatformServiceRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'invoice_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/invoice[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\InvoiceRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'final_invoice' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/invoice-deals',
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\InvoiceDealsRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'download_invoice' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/invoice/report[/:id]',
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\InvoiceReportRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            ),
            'user_invoice_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rc/api/invoice/user[/:id]',
                    'defaults' => array(
                        'module' => 'RateCard',
                        'controller' => 'RateCard\Controller\InvoiceUserRest',
                        'isRest'    => true,
                        'language'  => 'en'
                    )
                )
            )

        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'RateCard/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'module_layouts' => array(
        'RateCard' => 'rate_card/layout',
    ),
    'rate_card_upload_dir' => 'rate-card-app/uploads'
);
