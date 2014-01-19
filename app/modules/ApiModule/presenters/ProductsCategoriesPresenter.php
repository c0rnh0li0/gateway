<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse;

/**
 * Resource /productsimages API.
 * 
 * @author Lukas Bruha
 */
class ProductsCategoriesPresenter extends BasePresenter {

    /**
     * Returns allowed methods.
     * 
     * @return \Nette\Application\Responses\TextResponse
     */
    protected function getApiList() {

        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0/productscategories \t This help list. \n";
        $content .= "POST \t /api/rest/1.0/productscategories/<connection_name> \t Expects attached Etron XML file with products or standard 
                        POST data (for cURL use file=@<PATH_TO_FILE> or text=<TEXT>). \n";

        return new TextResponse($content);
    }

    /**
     * Method not implemented as in parent.
     * 
     */
    public function actionGet() {        
        $this->sendResponse(new TextResponse("GET method for specific connection not implemented."));
    }

    /**
     * Method not implemented as in parent.
     * 
     */    
    public function actionPut() {        
        $this->sendResponse(new TextResponse("PUT method for specific connection not implemented."));
    }
    
    /**
     * Method not implemented as in parent.
     * 
     */    
    public function actionDelete() { 
        $this->sendResponse(new TextResponse("DELETE method for specific connection not implemented."));
    }    
    
    /**
     * Give specific name of handler.
     * 
     * @param bool $lowercase
     * @return string
     */
    protected function getPureName($lowercase = false) {
        return 'products_categories';
    }

}
