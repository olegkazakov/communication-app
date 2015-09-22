<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Users;

use Users\Form\LoginFilter;
use Users\Form\LoginForm;
use Users\Form\RegisterFilter;
use Users\Form\RegisterForm;
use Users\Model\User;
use Users\Model\UserTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    public function getServiceConfig() {
      return array (
        'abstract_factories' => array(),
        'aliases' => array(),
        'factories' => array(
          'AuthService' => function($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user', 
              'email','password', 'MD5(?)');
          
          $authService = new AuthenticationService();
    						$authService->setAdapter($dbTableAuthAdapter);
    						return $authService;
            
          },
            
          'UserTable' => function($sm) {
            $tableGateway = $sm->get('UserTableGateway');
            $table = new UserTable($tableGateway);
            return $table;
          },
          'UserTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new User());
            return new TableGateway('user', $dbAdapter, null,
            $resultSetPrototype);
          },
          // Формы
          'LoginForm' => function ($sm) {
            $form = new LoginForm();
            $form->setInputFilter($sm->get('LoginFilter'));
            return $form;
          },
            
          'RegisterForm' => function ($sm) {
            $form = new RegisterForm();
            $form->setInputFilter($sm->get('RegisterFilter'));
            return $form;
          },
          
          //Фильтры
          'LoginFilter' => function ($sm) {
            return new LoginFilter();
          },
          
          'RegisterFilter' => function ($sm) {
            return new RegisterFilter();
          },
        ),
        'invokables' => array(),
        'services' => array(),
        'shared' => array(),
      );
      
    }
}
