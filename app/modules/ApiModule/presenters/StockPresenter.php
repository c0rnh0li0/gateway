<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse;

/**
 * Resource /products API.
 * 
 * @author Lukas Bruha
 */
class StockPresenter extends BasePresenter {

    /**
     * Returns allowed methods.
     * 
     * @return \Nette\Application\Responses\TextResponse
     */
    protected function getApiList() {

        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0/products \t This help list. \n";
        $content .= "POST \t /api/rest/1.0/products/<connection_name>[/<node>] \t Expects attached XML file or standard 
                        POST data (for cURL use file=@<PATH_TO_FILE> or text=<TEXT>) and optional node type ('erp' or 'shop') for direction. \n";
        $content .= "DELETE \t /api/rest/1.0/products/<connection_name>/<filename> \t Moves processed file to archive. \n";

        return new TextResponse($content);
    }

}
