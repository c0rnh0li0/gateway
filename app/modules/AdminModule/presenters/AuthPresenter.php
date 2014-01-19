<?php

namespace AdminModule;

use Yourface\Application\UI\Form;

/**
 * Authenticaton presenter.
 * 
 * @author Lukas Bruha
 * 
 */
class AuthPresenter extends \BasePresenter {

    const TYPE_EMAIL = 'email';
    const TYPE_USERNAME = 'username';

    protected $type = self::TYPE_USERNAME;

    /**
     * Redirects if authenticated.
     */
    public function startup() {
        // skip app's BasePresenter
        \Nette\Application\UI\Presenter::startup();

        $this->logger = $this->getService('FileLogger');

        $user = $this->getUser();

        $this->type = isset($this->context->parameters['security']) ? $this->context->parameters['security']['authType'] : self::TYPE_USERNAME;
        
        if ($user->isLoggedIn()) {
            $this->redirect('Default:default');
        }
    }

    ////////////////
    // COMPONENTS //
    ////////////////
    /**
     * Login form.
     * 
     */
    public function createComponentLoginForm() {
        $form = new Form($this, 'loginForm');

        if ($this->type == self::TYPE_EMAIL) {
            $form->addText('login', 'Email')
                    ->addRule(Form::FILLED, 'Enter login email')
                    ->addRule(Form::EMAIL, 'Filled value is not valid email');
        } else {
            $form->addText('login', 'Username')
                    ->addRule(Form::FILLED, 'Enter login username');
        }

        $form->addPassword('password', 'Password')
                ->addRule(Form::FILLED, 'Enter password');

        $form->addCheckbox('persistent', 'Remember me');
        
        $form->addSubmit('send', 'Log in');
        $form->onSuccess[] = callback($this, 'handleSignIn');

        $form->addProtection('Please submit this form again (security token has expired).');
    }

    /**
     * Authenticates user.
     * 
     * @param \Yourface\Application\UI\Form $form
     */
    public function handleSignIn(Form $form) {
        $values = $form->getValues();

        try {
            $user = $this->getUser();
            $user->login($values['login'], $values['password']);

            if ($values->persistent) {
                $user->setExpiration('+10 days', FALSE);
            } else {
                $user->setExpiration('+8 hours');
            }

            $this->flashMessage('You have been signed in.', 'success');
            $this->redirect('Default:default');
        } catch (\Nette\Security\AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }
    }

}
