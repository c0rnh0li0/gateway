<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse,
    Nette\Application\Responses\FileResponse,
    Yourface\Application\Responses\XmlResponse;

/**
 * Just prints out the API list.
 * 
 * @author Lukas Bruha
 */
class DefaultPresenter extends BasePresenter {

    /**
     * Returns an XML response including all files list.
     */
    protected function getApiList() {

        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0 \t This help list. \n";
        $content .= "GET \t /api/rest/1.0/<type>/<connection_name> \t Returns a list of exported XML files. \n";
        $content .= "GET \t /api/rest/1.0/<type>/<connection_name>/<filename> \t Downloads requested file. \n";
        $content .= "POST \t /api/rest/1.0/<type>/<connection_name> \t Expects attached XML file (for cURL use file=@<PATH_TO_FILE>). \n";
        $content .= "DELETE \t /api/rest/1.0/<type>/<connection_name>/<filename> \t Moves processed file to archive. \n";

        return new TextResponse($content);
    }
}
