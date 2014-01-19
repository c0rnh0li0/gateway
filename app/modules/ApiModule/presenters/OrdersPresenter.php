<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse;

/**
 * Resource /orders API.
 * 
 * @author Lukas Bruha
 */
class OrdersPresenter extends BasePresenter {

    /**
     * Returns allowed methods.
     * 
     * @return \Nette\Application\Responses\TextResponse
     */
    protected function getApiList() {
        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0/orders \t This help list. \n";
        $content .= "GET \t /api/rest/1.0/orders/<connection_name> \t Returns a list of downloadable XML exports. \n";
        $content .= "GET \t /api/rest/1.0/orders/<connection_name>/<filename> \t Downloads requested file. \n";
        $content .= "POST \t /api/rest/1.0/orders/<connection_name>[/<node>] \t Expects attached XML file or standard 
                        POST data (for cURL use file=@<PATH_TO_FILE> or text=<TEXT>) and optional node type ('erp' or 'shop') for direction. \n";
        $content .= "DELETE \t /api/rest/1.0/orders/<connection_name>/<filename> \t Moves processed file to archive. \n";

        return new TextResponse($content);
    }

}
