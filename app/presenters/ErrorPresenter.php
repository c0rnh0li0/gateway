<?php

use Nette\Diagnostics\Debugger;

/**
 * Error presenter to handle bad requests mainly.
 * 
 * @author Lukas Bruha
 */
class ErrorPresenter extends BasePresenter {

    public function renderDefault($exception) {          
        if ($this->isAjax()) { // AJAX request? Just note this error in payload.
            $this->getPayload()->error = TRUE;
            $this->terminate();
        } elseif ($exception instanceof \Nette\Application\BadRequestException) {
            $this->setView('404'); // load template 404.phtml
            $this->template->text = $exception->getMessage();
            $this->template->referer = $this->getHttpRequest()->getUrl();
        } else {
            $this->setView('500'); // load template 500.phtml
            $this->template->title = 'Error 500: Internal Server Error';

            //\Nette\Diagnostics\Debugger::toStringException($exception); // and handle error by Nette\Debug
        }
    }

}
