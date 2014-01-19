<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse;

/**
 * Files uploading API, /files resource.
 * 
 * @author Lukas Bruha
 */
class FilesPresenter extends BasePresenter {

    /**
     * Returns allowed methods.
     * 
     * @return \Nette\Application\Responses\TextResponse
     */
    protected function getApiList() {

        $content = "Available GW2 REST API methods \n\n";

        $content .= "GET \t /api/rest/1.0/files \t This help list. \n";
        $content .= "POST \t /api/rest/1.0/files/<connection_name> \t Expects attached file to be uploaded.  \n";
        $content .= "DELETE \t /api/rest/1.0/files/<connection_name>/<filename>/delete \t Deletes given file ('delete' postfix required for .jpg, .png). \n";
        $content .= "DELETE \t /api/rest/1.0/files/<connection_name>/<filename>* \t Deletes given file (* postfix required for .jpg, .png and .gif). \n";

        return new TextResponse($content);
    }

    public function actionPost() {
        try {
            $this->validateConnection();
            
            $dest = $this->getFolder($this->connection, self::FOLDER_INPUT);
            
            ///////////////
            // POST DATA //
            ///////////////
            $files = $this->getRequest()->getFiles();
           
            // both files and post were not transferred - that is not allowed
            if (!count($files)) {
                throw new \Nette\FileNotFoundException('Missing files to pass.');
            }
            
            $files = current($files);

            if (!is_array($files)) {
                $files = array($files);
            }

            $fileNames = array();
            
            // process all files
            // FIXME if one file is bad, cancel or skip?
            foreach ($files as $file) {
                // something wrong happened during transfer
                if (!$file->isOk()) {
                    throw new \Nette\IOException('An unexpected error raised during the file transfer.');
                }

                // file is empty
                if (!$file->getSize()) {
                    throw \Nette\IOException('Upload is not possible - transferred file is empty.');
                }

                /////////////////
                // SAVING DATA //
                /////////////////                
                $fileNames[] = $file->getName();
                $path = $dest . DIRECTORY_SEPARATOR . $file->getName();
                
                // NOTICE: if file exists, it is overwritten
                if (!$file->move($path)) {
                     throw \Nette\IOException('Upload is not possible - cannot copy the file.');
                }
            }

            $response = sprintf("File(s) '%s' for connection '%s' was uploaded successfully to '%s'.", implode(", ", $fileNames), $this->connection, $dest);
        } catch (\Exception $e) {
            $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S400_BAD_REQUEST);
            $response = $e->getMessage();
        }

        // send response
        $this->sendResponse(new TextResponse($response));
    }

    /**
     * Method GET not implemented as in parent.
     * 
     */
    public function actionGet() {        
        $this->sendResponse(new TextResponse("GET method for specific connection not implemented."));
    }

    /**
     * Deletes given file.
     * 
     */
    public function actionDelete() {        
        //$this->sendResponse(new TextResponse("DELETE method for specific connection not implemented."));
        
        $output = $this->getFolder($this->connection, self::FOLDER_INPUT);
        $fileName = $this->fileName;
        $fileName = str_replace("*", "", $fileName);

        // get requested file
        $files = \Nette\Utils\Finder::findFiles($fileName)->in($output);
        $response = sprintf("File '%s' was not found in '%s'.", $fileName, $output);

        foreach (\Nette\Utils\Finder::findFiles($fileName)->in($output) as $file) {
            if (@unlink((string) $file)) {
                $response = sprintf("File '%s' has been deleted.", $fileName);
            } else {
                $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S400_BAD_REQUEST);
                $response = sprintf("File '%s' cannot be deleted for some reason.", $fileName);
            }
            
            break;
        }
        
        $this->sendResponse(new TextResponse($response));        
    }
    
    /**
     * Method PUT not implemented as in parent.
     * 
     */
    public function actionPut() {        
        $this->sendResponse(new TextResponse("PUT method for specific connection not implemented."));
    }
    
}
