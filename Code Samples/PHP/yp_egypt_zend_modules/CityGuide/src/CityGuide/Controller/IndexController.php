<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/CityGuide for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CityGuide\Controller;

use Zend\View\Model\ViewModel;
use yellow\Models\Web\YpConfig;
use yellow\Abstracts\YPAbstractController;
use Zend\Validator\Regex as RegexValidator;
use Application\Model\Cdn;
use Application\Model\Uri;

class IndexController extends YPAbstractController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $language = $this->params('language');
        $cityModel = new \yellow\Models\Web\City();
        $cityModel->setLanguage($language);
        $cityId = $this->params('cityId');
        if(is_null($cityId)) {
            $configModel = new YpConfig();
            $config = current($configModel->getConfigById(YpConfig::DEFAULT_CITY_LANDING_PAGE));
            $cityId = $config->value;
            $city = current($cityModel->getCityById($cityId));
        } else {
            $city = current($cityModel->getCityByName($cityId));
            if(!$city) {
            	$configModel = new YpConfig();
            	$config = current($configModel->getConfigById(YpConfig::DEFAULT_CITY_LANDING_PAGE));
            	$cityId = $config->value;
            	$city = current($cityModel->getCityById($cityId));
            }
        }
        $categoryModel = new \yellow\Models\Web\Category();
        $categoryModel->setLanguage($language);
        $popularCategories = $categoryModel->getPopularCategories();
        $popularCities = $cityModel->getPopularCities();
        $cloud = $this->generateTagCloud($popularCategories, 20, 40);
        $view->setVariable('language', $language);
        $view->setVariable('city', $city);
        $view->setVariable('popularCategories', $popularCategories);
        $view->setVariable('cloud', $cloud);
        $sideContent = '<h3 class="yellowBubble">'.$this->translator->translate('othercities').'</h3>';
        foreach($popularCities as $popularCity) {
            $sideContent .= '<div><a href="'.$this->url()->fromRoute('city-guide/default', array('language' => $language , 'cityId' => $popularCity->name)).'">'.$popularCity->name.'</a></div>';
        }
        $this->layout()->setVariable('sideContent', $sideContent);
        $view->setVariable('city', $city);
        return $view;
    }
    
    
    public function oldCityAction () { 
    	
    	$cityName = $this->params("city"); 
    	
    	$newCity = ""; 
    	if (preg_match("/(riyadh)/", $cityName)){ 
    		$newCity="الرياض.html";
    	}
    	if (preg_match("/(jeddah)/", $cityName)){
    		$newCity="جدة.html";
    	}
    	if (preg_match("/(makkah)/", $cityName)){
    		$newCity="مكة%20المكرمة.html";
    	}
    	if (preg_match("/(madinah)/", $cityName)){
    		$newCity="المدينة%20المنورة.html";
    	}
    	if (preg_match("/(khobar)/", $cityName)){
    		$newCity="الخبر.html";
    	}
    	if (preg_match("/(dammam)/", $cityName)){
    		$newCity="الدمام.html";
    	}
    	if (preg_match("/(abha)/", $cityName)){
    		$newCity="ابها.html";
    	}
    	header("Location: /ar/cityguide/$newCity",TRUE,301);
    	exit; 
    }
    
    public function oldHomeAction () { 
    	header("Location: /ar/cityguide/city_guide.html",TRUE,301);
    	exit;
    }
    public function aboutAction()
    {
    	$view = new ViewModel();
    	$language = $this->params('language');
    	$cityId = $this->params('cityId');
    	if(is_null($cityId)) {
    		$configModel = new YpConfig();
    		$config = current($configModel->getConfigById(YpConfig::DEFAULT_CITY_LANDING_PAGE));
    		$cityId = $config->value;
    	}
    	$cityModel = new \yellow\Models\Web\City();
    	$cityModel->setLanguage($language);
    	$city = current($cityModel->getCityByName($cityId));
    	$view->setVariable('city', $city);
    	return $view;
    }
    
    public function addQuestionAction() {
        $response = $this->getResponse();
        $returnObject = new \stdClass();
        $errors = array();
        $headers = $this->getRequest()->getHeaders();
        $session = new \Zend\Session\Container();
        $sessionManager = $session->getManager();
        $server = $this->getRequest()->getServer();
        $question = new \yellow\DataObjects\Web\GetInTouch();
        $questionModel = new \yellow\Models\Web\GetInTouch();
        $question->language = $this->params('language');
        $question->name = $this->params()->fromPost('name');
        $question->email = $this->params()->fromPost('email');
        $question->question = $this->params()->fromPost('question');
        $question->cityId = $this->params()->fromPost('cityId');
        $question->userId = 0;
        $question->entryDate = date('Y-m-d H:i:s');
        $question->screenResolution = $this->params()->fromPost('screenResolution');
        $question->sessionId = $sessionManager->getId();
        $question->userAgent = $headers->get('user-agent')->value;
        $question->ipAddress = $server->get('REMOTE_ADDR');
        if($question->name == '') {
            $error = new \stdClass();
            $error->field = 'name';
            $error->message = $this->translator->translate('plz').' '.$this->translator->translate('name');
            $errors[] = $error;
        }
        $emailValidator = new RegexValidator ('/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/');
        if($question->email == '' || !$emailValidator->isValid($question->email)) {
            $error = new \stdClass();
            $error->field = 'email';
            $error->message = $this->translator->translate('plz').' '.$this->translator->translate('validemail');
            $errors[] = $error;
        }
        if($question->question == '') {
            $error = new \stdClass();
            $error->field = 'question';
            $error->message = $this->translator->translate('plz').' '.$this->translator->translate('yourquestion');
            $errors[] = $error;
        }
        if(count($errors) > 0) {
        	$returnObject->status = 'FAILED';
        	$returnObject->errors = $errors;
        } else {
            $questionModel->insertQuestion($question);
            $emailModel = new \yellow\Models\Web\Email();
            $emailModel->setLanguage($question->language);
            $email = current($emailModel->getEmailById(7));
            $email->replace("{NAME}", $question->name);
            $email->replace("{EMAIL}", $question->email);
            $email->replace("{MESSAGE}", $question->question);
            $email->replace("{CITY_NAME}", $question->cityId);
            $email->replace("{ALIGNMENT}", $alignment);
            $email->replace("{SITE_URL}", "");
            $email->replace("{LOGO}", Uri::$COMPANY_LOGO);
            $email->replace("{URL}", Uri::$SITE_URL);
            $email->send();
        	$returnObject->status = 'SUCCEEDED';
        	$returnObject->message = $this->translator->translate('msg_succ');
        }
        $response->setContent(\Zend\Json\Json::encode($returnObject));
        return $response;
    }
    
    private function generateTagCloud($categories, $minSize, $maxSize) {
        $categoriesCount = count($categories);
        $minimumCount = $categories[0]->hitCount;
        $maximumCount = $categories[0]->hitCount;
        foreach($categories as $category):
            if($category->hitCount < $minimumCount) {
                $minimumCount = $category->hitCount;
            } elseif($category->hitCount > $maximumCount) {
                $maximumCount = $categories->hitCount;
            }
        endforeach;
        $spread = $maximumCount - $minimumCount;
        if($spread == 0) $spread = 1;
        $cloudTags = array(); // create an array to hold tag code
        foreach ($categories as $category) {
        	$size = $minSize + ($category->hitCount - $minimumCount) * ($maxSize - $minSize) / $spread;
        	$category->fontSize = $size;
        	$cloudTags[] = $category;
        }
        return $cloudTags;
    }
}
