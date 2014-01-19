<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse,
    Gateway\Utils;

/**
 * Logs retrieving API, resource /logs.
 * 
 * @author Lukas Bruha
 */
class LogsPresenter extends BasePresenter {

    /**
     * Returns allowed methods.
     * 
     * @return \Nette\Application\Responses\TextResponse
     */
    protected function getApiList() {

        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0/logs \t This help list. \n";
        $content .= "GET \t /api/rest/1.0/logs/<connection_name> \t Returns a list of files..  \n";
        $content .= "GET \t /api/rest/1.0/logs/<connection_name>/<filename> \t Downloads requested file. \n";

        return new TextResponse($content);
    }
    
    /**
     * Allowed mask for log files.
     * 
     * @return string
     */
    protected function getAllowedFileMask() {
        return '*.log';
    }
    
    /**
     * Returns all allowed files from directory.
     * 
     * @return array
     */
    protected function getFiles() {
        $sourceFolder = Utils::getConnectionLogDir($this->connection);
        $files =  \Nette\Utils\Finder::findFiles($this->getAllowedFileMask())->in($sourceFolder);
        
        return $files;
    }
    
    /**
     * Method POST not implemented as in parent.
     */
    public function actionPost() {        
        $this->sendResponse(new TextResponse("POST method for specific connection not implemented."));
    }

    /**
     * Method DELETE not implemented as in parent.
     */
    public function actionDelete() {        
        $this->sendResponse(new TextResponse("DELETE method for specific connection not implemented."));
    }
    
    /**
     * Method PUT not implemented as in parent.
     */
    public function actionPut() {        
        $this->sendResponse(new TextResponse("PUT method for specific connection not implemented."));
    }
    
}
