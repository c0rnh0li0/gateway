<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse,
    Yourface\Application\Responses\XmlResponse,
    Gateway\Utils;

/**
 * Logs retrieving API.
 * 
 * @author Lukas Bruha
 */
class SchedulesPresenter extends BasePresenter {

    /**
     * Returns allowed methods.
     * 
     * @return \Nette\Application\Responses\TextResponse
     */
    protected function getApiList() {
        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0/schedules \t This help list. \n";
        $content .= "GET \t /api/rest/1.0/schedules/<connection_name> \t Returns a list of schedules..  \n";
        
        return new TextResponse($content);
    }
    
    /**
     * Schedules in XML response.
     * 
     */
    public function actionGet() {
        $schedules = $this->getService('database')
                            ->table('gw_schedule')
                            ->where('gw_connection.name = ?', $this->connection)
                            ->order('gw_schedule.id DESC');
        
        $xml = new \SimpleXMLElement('<root/>');
        $xml->addAttribute('erp', $this->erp);
         
        foreach ($schedules as $schedule) {
            $name = $schedule->id;
            $created = $schedule->inserted_at;
            $processed = $schedule->executed_at;
            $finished = $schedule->finished_at;
            $status = $schedule->status;
            
            $elem = $xml->addChild("item", $status);
            $elem->addAttribute('id', $name);
            
            $elem->addAttribute('is_cancelled', $schedule->is_cancelled ? 1 : 0);
            $elem->addAttribute('created_at', $created);
            $elem->addAttribute('processed_at', $processed);
            $elem->addAttribute('finished_at', $processed);
        }
        
        $this->sendResponse(new XmlResponse($xml->asXML()));
    }

    /**
     * Method not implemented as in parent.
     */
    public function actionPost() {        
        $this->sendResponse(new TextResponse("POST method for specific connection not implemented."));
    }

    /**
     * Method not implemented as in parent.
     */
    public function actionDelete() {        
        $this->sendResponse(new TextResponse("DELETE method for specific connection not implemented."));
    }
    
    /**
     * Method not implemented as in parent.
     */
    public function actionPut() {        
        $this->sendResponse(new TextResponse("PUT method for specific connection not implemented."));
    }
    
}
