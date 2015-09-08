<?php

namespace Users\Controller;

use Users\Form\RegisterForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RegisterController extends AbstractActionController {

  public function indexAction() {
    $form = new RegisterForm();
    $viewModel = new ViewModel(array('form' => $form));
    return $viewModel;
  }

  public function confirmAction() {
    $viewModel = new ViewModel();
    return $viewModel;
  }

}
