<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse;

/**
 * Resource /categories API.
 * 
 * @author Lukas Bruha
 */
class CategoriesPresenter extends BasePresenter {

    /**
     * Returns allowed methods.
     * 
     * @return \Nette\Application\Responses\TextResponse
     */
    protected function getApiList() {

        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0/categories \t This help list. \n";
        $content .= "POST \t /api/rest/1.0/categories/<connection_name> \t Expects attached XML file or standard 
                        POST data (for cURL use file=@<PATH_TO_FILE> or text=<TEXT>). \n";

        return new TextResponse($content);
    }
    
    public function actionGet() {        
        $this->sendResponse(new TextResponse("GET method for specific connection not implemented."));
    }

    public function actionPut() {        
        $this->sendResponse(new TextResponse("PUT method for specific connection not implemented."));
    }
    
    public function actionDelete() { 
        $this->sendResponse(new TextResponse("DELETE method for specific connection not implemented."));
    }        

}
