<?php

namespace Yourface\Application\Responses;

use Nette;

/**
 * XML response used mainly for API requests.
 *
 * @author Lukas Bruha
 */
class XmlResponse extends Nette\Object implements Nette\Application\IResponse {

    protected $content = null;
    
    protected $contentType = null;
    
    /**
     * @param  string content
     * @param  string MIME content type
     */
    public function __construct($content, $contentType = NULL) {
        $this->content = $content;
        $this->contentType = $contentType ? $contentType : 'text/xml';
    }

    /**
     * Sends response to output.
     * @return void
     */
    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse) {
        $httpResponse->setContentType($this->contentType);
        $httpResponse->setExpiration(FALSE);
        echo $this->content;
    }

}
