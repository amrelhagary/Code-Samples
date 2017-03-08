<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
set_time_limit(-1);
date_default_timezone_set('Africa/Cairo');
define('ENV','dev');

return array(
    'websites' => array(
        'PS'  => array(
            array(
                'page_name' => 'ps yellow pages',
                'page_url' => 'http://www.yellowpages.com.ps/',
                'page_code' => 'HP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'find'
                    )
                )
            ),
            array(
                'page_name' => 'ps category pages',
                'page_url' => 'http://www.yellowpages.com.ps/category/Advertising-Agencies/NDBfX18gX19TZWFyY2ggQ2F0ZWdvcnlfXyBf/',
                'page_code' => 'CP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'search-result-title'
                    )
                )
            ),
            array(
                'page_name' => 'ps search pages',
                'page_url' => 'http://www.yellowpages.com.ps/search/eWVsbG93IHBhZ2Vz/search.html',
                'page_code' => 'SP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'body'
                    )
                )
            )
        ),
        'EYP' => array(
            array(
                'page_name' => 'yellow home page',
                'page_url' => 'http://www.yellowpages.com.eg/en',
                'page_code' => 'HP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'atYourFingerTips',
                    ),
//                    'preg_match' => array(
//                        'dom_element_name' => 'class',
//                        'dom_element_value' => 'atYourFingerTips',
//                    ),
                ),
                'contacts' => array()
            ),
            array(
                'page_name' => 'yellow categories page',
                'page_url' => 'http://www.yellowpages.com.eg/en/category/cars-_-dealers,-new-cars',
                'page_code' => 'CP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Modern Motors - Nissan',
                    ),
                ),
            ),
//            array(
//                'page_name' => 'yellow search page',
//                'page_url' => 'http://www.yellowpages.com.eg/en/search/cars?',
//                'page_referer' => 'http://www.yellowpages.com.eg/en',
//                'page_code' => 'SRP',
//                'search' => array(
//                    'xpath' => array(
//                        // add xpath for the element
//                        'dom_element_xpath' => '//*[@id="mainContent"]/div/div[1]/div[5]/ul/li',
//                        'dom_element_value' => '',
//                        // expected return value
//                        'counter' => 20
//                    ),
//                ),
//            ),
        ),
        'LS' => array(
            array(
                'page_name' => 'localsearch homepage',
                'page_url' => 'http://www.localsearch.ae/en',
                'page_code' => 'HP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Home',
                    ),
                ),
            ),
            array(
                'page_name' => 'localsearch search page',
                'page_url' => 'http://www.localsearch.ae/en/search/uae/starbucks',
                'page_referer' => 'http://www.localsearch.ae/en',
                'page_code' => 'SP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Coffee',
                    ),
                ),
            ),
            array(
                'page_name' => 'localsearch search page',
                'page_url' => 'http://www.localsearch.ae/en/search/uae/starbucks',
                'page_referer' => 'http://www.localsearch.ae/en',
                'page_code' => 'SP',
                'search' => array(
                'xpath' => array(
                        'dom_element_xpath' => '/html/body/div[3]/div[2]/div[2]/div[2]/ul/li',
                        'dom_element_value' => '',
                        'counter' => 20
                    ),
                ),
            ),
            array(
                'page_name' => 'localsearch category page',
                'page_url' => 'http://www.localsearch.ae/en/category/Restaurants/1873',
                'page_referer' => 'http://www.localsearch.ae/en',
                'page_code' => 'CP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Pizza'
                    ),
                ),
            ),
            array(
                'page_name' => 'localsearch static page',
                'page_url' => 'http://www.localsearch.ae/en/privacy_policy.html',
                'page_referer' => 'http://www.localsearch.ae/en',
                'page_code' => 'StaticP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Privacy and Security Policy'
                    ),
                ),
            ),
        ),
        'TYP' => array(
            array(
                'page_name' => 'turkey yellow home page',
                'page_url' => 'http://www.yellowpages.com.tr/en/',
                'page_code' => 'HP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Search'
                    ),
                ),
            ),
            array(
                'page_name' => 'turkey search page',
                'page_url' => 'http://www.yellowpages.com.tr/en/search/cars/Y2Fyc19fX19fX19fMF8gXyBfIF8gX18=/',
                'page_referer' => 'http://www.yellowpages.com.tr/en',
                'page_code' => 'SP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'cars'
                    ),
                ),
            ),
            array(
                'page_name' => 'turkey category page',
                'page_url' => 'http://www.yellowpages.com.tr/en/category/cars-used/Q2FycyBVc2VkXzBfMF8wX19fX2NhdGVnb3JpZXNfMF9fX19fX19fNzI1Xw==',
                'page_referer' => 'http://www.yellowpages.com.tr/en',
                'page_code' => 'CP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Cars Used'
                    ),
                ),
            ),
            array(
                'page_name' => 'turkey static page',
                'page_url' => 'http://www.yellowpages.com.tr/en/contactus.html',
                'page_code' => 'StaticP',
                'search' => array(
                    'strpos' => array(
                        'dom_element_name' => 'Add your FREE business listing'
                    ),
                ),
            ),
            array(
                'page_name' => 'turkey cdn',
                'page_url' => 'http://typ1.iypcdn.com/static/includes/language1/images/yellow-logo-in.png',
                'page_code' => 'CDN',
                'http_response_code' => '200'
            ),
        ),
        'test' => array(
//            array(
//                'page_name' => 'test name',
//                'page_url' => 'http://192.168.33.11/YellowMonitor/long.php',
//                'page_code' => 'TST',
//                'search' => array(
//                    'strpos' => array(
//                        'dom_element_name' => 'hello'
//                    )
//                )
//            ),
//            array(
//                'page_name' => 'test name',
//                'page_url' => 'http://192.168.33.11/YellowMonitor/long.php',
//                'page_code' => 'TST',
//                'http_response_code' => '200'
//            )
//            array(
//                'page_name' => 'test name',
//                'page_url' => 'http://192.168.33.11/YellowMonitor/redirect.php',
//                'page_code' => 'TST',
//                'http_response_code' => '200'
//            )
        )
    ),
    'webservices' => array(
        'Blackberry' => array(
            'url' => 'http://service.yellowpages.com.eg/SMS_LIVE_1/monitor_blackberry.php?search=car&cache=1&rand='.rand(1, 1000),
            'expected_response' => '20',
            'code' => 'BB'
        ),
        'Vodafone' => array(
            'url' => 'http://service.yellowpages.com.eg/SMS1_1/vodafone_monitor.php?search=car&cache=1',
            'expected_response' => '5',
            'code' => 'VODA'
        ),
        'Mobinil' => array(
            'url' => 'http://service.yellowpages.com.eg/SMS_LIVE_1/monitor_mobinil.php?search=car&cache=1',
            'expected_response' => '20',
            'code' => 'MOBI'
        ),
        'Egov' => array(
            'url' => 'http://service.yellowpages.com.eg/SMS_LIVE_1/monitor_egov.php?search=car&cache=1',
            'expected_response' => '20',
            'code' => 'EGOV'
        ),
        'Daleeli' => array(
            'url' => 'http://service.daleeli.com/webservice/iphone/monitor_iphone.php?search=cars&fds',
            'expected_response' => '30',
            'code' => 'DALI'
        ),
//        'test' => array(
//            'url' => 'http://192.168.33.11/YellowMonitor/long.php',
//            'expected_response' => 'OK',
//            'code' => 'TST SRV'
//        )
    ),
    'contacts' => array(
        'amr elhagary' => array(
            'email'  => 'a.elhagary@yellow.com.eg',
            'mobile' => '+2123456789'
        ),
    ),
    'sms' => array(
        "from" => "YellowPages",
        "username" => "user",
        "password" => "password",
        "api_id" => "123"
    ),
    'email' => array(
        'method' => (defined('ENV') && ENV == 'prod') ? 'sendmail': 'smtp', // method take values sendmail or smtp
        'from' => 'noreply@yellow.com.eg',
        'subject' => 'Yellow Monitor Reporting Service'
    ),
    'smtp' => array(
        'host' => 'smtp.gmail.com',
        'username' => 'user',
        'password' => 'pass',
        'port' => 587,
        'ssl' => 'tls'
    ),
    'notification' => array(
        'stop' => array(
            'from' => 'Friday 1:00:00',
            'to'   => 'Friday 6:00:00'
        )
    )
);