<?php

namespace ApiModule;

use Nette\Application\Responses\TextResponse,
    Nette\Application\Responses\FileResponse,
    Yourface\Application\Responses\XmlResponse,
    Gateway\IConnection,
    Nette\Diagnostics\Debugger,
    Gateway\Handler\ISource;

/**
 * Parent of all REST API classes.
 * 
 * @author Lukas Bruha
 * 
 */
abstract class BasePresenter extends \BasePresenter {

    const FOLDER_INPUT = 'put';
    const FOLDER_OUTPUT = 'get';

    /** @persistent */
    public $erp = 'etron';

    /** @persistent */
    public $connection;

    /** @persistent */
    public $fileName;

    /** @persistent */
    public $node = IConnection::NODE_ERP;

    /**
     * Store folder masks for current ERP.
     * Key is the folder type.
     * 
     * @var array 
     */
    protected $folderMasks = array();

    /**
     * Stores gateway storage configuration.
     * 
     * @var stdClass
     */
    protected $gwStorage = null;
    
    /**
     * Stores gateway api configuration.
     * 
     * @var stdClass
     */
    protected $gwApi = null;    

    /**
     * Init GW storage, folder masks and loads connections and adapters.
     * 
     * @throws \Nette\FatalErrorException
     */
    public function startup() {
        parent::startup();

        $this->gwStorage = \Nette\Environment::getConfig('gateway')->storage;
        $this->gwApi= \Nette\Environment::getConfig('gateway')->api;
        
        // disable debugger for API
        Debugger::enable(false);
        Debugger::$consoleMode = true;
        
        if (!$this->gwStorage) {
            throw new \Nette\FatalErrorException("Configuration required for Gateway 'storage' in config.neon.");
        }
        
        if (!$this->gwApi) {
            throw new \Nette\FatalErrorException("Configuration required for Gateway 'api' in config.neon.");
        }

        $this->initFolderMasks();
    }

    /**
     * Prints out current resource API list.
     * 
     */
    public function actionDefault() {
        $this->sendResponse($this->getApiList());
    }

    /**
     * Downloads XML according to given connection name.
     * 
     */
    public function actionGet() {
        try {
            // check if connection exists
            $this->validateConnection();

            $files = $this->getFiles();

            // specific file was requested
            if ($this->fileName) {
                $response = $this->getResponseFile($files);
            } else {
                // list of files requested
                $response = $this->getResponseList($files);
            }
        } catch (\Exception $e) {
            $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S400_BAD_REQUEST);
            $response = new TextResponse($e->getMessage());
        }

        $this->sendResponse($response);
    }
    
    /**
     * Uploads file.
     * 
     * FIXME restrict specific file types
     */
    public function actionPost() {
        try {
            // we check if connection does exists
            $this->validateConnection();
            $this->validateNode();

            /////////////////////
            // FOLDER SETTINGS //
            /////////////////////
            $dest = $this->getFolder($this->connection, self::FOLDER_INPUT);
            
            ///////////////
            // POST DATA //
            ///////////////
            $files = $this->getRequest()->getFiles();
            $post = $this->getRequest()->getPost();
            $sourceType = null;

            // both files and post were not transferred - that is not allowed
            if (!count($files) && !count($post)) {
                throw new \Nette\FileNotFoundException('Missing files or post data to pass.');
            }
            
            // 1) we work with files from POST
            if (count($files)) {
                $sourceType = ISource::TYPE_FILE;
                $file = current($files);

                // something wrong happened during transfer
                if (!$file->isOk()) {
                    throw new \Nette\IOException('An unexpected error raised during the file transfer.');
                }

                // file is empty
                if (!$file->getSize()) {
                    throw \Nette\IOException('Upload is not possible - transferred file is empty.');
                }

                // correct filename
                $connPath = \Gateway\Utils::generateFilename($file->getName());
                $dest = $dest . DIRECTORY_SEPARATOR . $connPath;
                
                $file->move($dest);
                
                // do not store big files (filesize > allowed limit) into DB
                if (isset($this->getApi()->source->file->limit) && ($this->getApi()->source->file->limit <= $file->size)) {
                    // big file -> FS storage and to DB only filepath
                    $sourceType = ISource::TYPE_FILEPATH;
                    $content = $dest;
                } else {
                    // small file -> DB storage
                    $content = file_get_contents($dest);
                }

            } else { // 2) no files in but standard data in POST
                $sourceType = ISource::TYPE_TEXT;
                $content = current($post);
                
                $connPath = \Gateway\Utils::generateFilename("json");
                $dest = $dest . DIRECTORY_SEPARATOR . $connPath;
                
                // FIXME now we expects only JSON format but it should be detected
                file_put_contents($dest, $post);
            }

            // SCHEDULE
            // we create a new plan in scheduler
            $connectionId = $this->getService('database')
                    ->table('gw_connection')
                    ->where(array('name' => $this->connection))
                    ->fetch();

            // CREATING SOURCES TO PLAN IN SCHEDULE
            $sourceHandler = $this->getService('database')
                            ->table('gw_handler')
                            ->select('gw_handler.id, class, gw_adapter:settings')
                            ->where(array(
                                'node' => $this->node, // read from node ERP or SHOP? ERP by default
                                'type' => $this->getPureName(true), // class name 
                                'stream' => IConnection::STREAM_READER, // always will be READER
                                'gw_adapter:gw_connection_id' => $connectionId
                            ))->fetch();
            
            // handler does not exist for this type (products, customers, orders...)
            if (!$sourceHandler) {
                // remove file
                @unlink($dest);

                throw new \Nette\InvalidArgumentException(sprintf("Handler '%s' to read from '%s' is not defined for '%s'.", $this->getPureName(true), $this->node, $this->connection));
            }

            // we create reader's class instance and validate given input
            $readerClass = $sourceHandler->class;
            $reader = new $readerClass(new \Gateway\Handler\Options($sourceHandler->settings));
            $reader->validate($content);

            // ADDING PLAN TO SCHEDULE
            $data = array(
                'gw_connection_id' => $connectionId,
                'inserted_at' => new \Nette\Database\SqlLiteral('NOW()'),
            );

            $scheduleId = $this->getService('database')
                    ->table('gw_schedule')
                    ->insert($data);

            // ADDING SOURCE OF SCHEDULE
            $sourceData = array(
                'gw_schedule_id' => $scheduleId,
                'gw_handler_id' => $sourceHandler->id,
                'content' => $content,
                'type' => $sourceType,
            );

            $this->getService('database')
                    ->table('gw_source')
                    ->insert($sourceData);

            // reponse message
            //$response = sprintf("File '%s' (size %s B) for connection '%s' has been put to '%s'.", $file->getName(), $file->getSize(), $this->connection, $dest);
            if (($sourceType == \Gateway\Handler\ISource::TYPE_FILE) || ($sourceType == \Gateway\Handler\ISource::TYPE_FILEPATH)) {
                $info = sprintf("%s, size of %s B)", $file->getName(), $file->getSize());
            } else {
                $info = sprintf("size %s B", mb_strlen($content));
            }

            $response = sprintf("Schedule for '%s' connection and '%s' reader was put into plan successfully (%s).", $this->connection, $this->node, $info);
        } catch (\Exception $e) {
            $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S400_BAD_REQUEST);
            $response = $e->getMessage();
        }

        // send response
        $this->sendResponse(new TextResponse($response));
    }

    /**
     * Confirms file as downloaded and moves it to archive 
     * so it will not be visible anymore.
     * 
     */
    public function actionDelete() {
        // get output folder
        $output = $this->getFolder($this->connection, self::FOLDER_OUTPUT);
        $fileName = $this->fileName;

        // get requested file
        $fileProcessed = false;
        $response = '';

        foreach (\Nette\Utils\Finder::findFiles($fileName)->in($output) as $file) {        
            // get archive folder and move file into that
            $archive = $this->getFolder($this->connection, self::FOLDER_OUTPUT, true);

            if (rename((string) $file, $archive . DIRECTORY_SEPARATOR . $file->getFileName())) {
                $fileProcessed = true;
            } else {
                $response = sprintf("Cannot move file '%s' to archive. Permission denied.", $fileName);
            }
            
            break;
        }

        // file processed check
        if ($fileProcessed) {
            $response = sprintf("File '%s' has been moved to archive.", $fileName);
        } else {
            $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S400_BAD_REQUEST);
            $response = $response ? $response : sprintf("File '%s' does not exist.", $fileName);
        }

        $this->sendResponse(new TextResponse($response));
    }

    /**
     * Handles PUT requests.
     * 
     */
    public function actionPut() {
        $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S501_NOT_IMPLEMENTED);
        $this->sendResponse(new TextResponse("PUT method is not supported yet."));
    }

    /////////////
    // HELPERS //
    /////////////    
    /**
     * Inits both input and output folders.
     * 
     */
    protected function initFolderMasks() {
        $storageRoot = realpath($this->getStorage()->root);

        $this->folderMasks[self::FOLDER_INPUT] = $storageRoot . '/' . $this->getStorage()->{$this->erp}->inputFolderMask; // expecting something like /etron/<CONNECTION_NAME>/put
        $this->folderMasks[self::FOLDER_OUTPUT] = $storageRoot . '/' . $this->getStorage()->{$this->erp}->outputFolderMask; // expecting something like /etron/<CONNECTION_NAME>/get                
    }

    /**
     * Returns current resource API list.
     * 
     */
    abstract protected function getApiList();

    /**
     * Generates, creates and returns a folder for specified connection and folder type.
     *  
     * @param string $connection
     * @param string $type
     * @return string
     * @throws \Nette\IOException
     */
    protected function getFolder($connection, $type, $archiveFolder = false) {
        $folder = sprintf($this->folderMasks[$type], $connection);
        $folder .= DIRECTORY_SEPARATOR . $this->getPureName(true);

        if ($archiveFolder) {
            $folder .= $this->getStorage()->{$this->erp}->archiveFolder;
        }

        if (!is_dir($folder)) {
            if (!@mkdir($folder, 0777, true)) {
                throw new \Nette\IOException(sprintf("Cannot create folder of '%s'. Please, check permissions.", $folder));
            }
        }

        return $folder;
    }

    /**
     * Gateway storage getter.
     * 
     * @return object
     */
    protected function getStorage() {
        return $this->gwStorage;
    }

    /**
     * Gateway api settings getter.
     * 
     * @return object
     */
    protected function getApi() {
        return $this->gwApi;
    }
    
    /**
     * Returns allowed file masks for this ERP.
     * 
     * @return string
     */
    protected function getAllowedFileMask() {
        return $this->getStorage()->{$this->erp}->allowedFileMasks;
    }

    /**
     * Returns connections list.
     * 
     * @return \Gateway\Connections
     */
    protected function getConnections() {
        return \Gateway\Connections::getInstance();
    }

    /**
     * Checks if connection exists.
     * 
     * @throws \Nette\InvalidArgumentException
     */
    protected function validateConnection() {
        ////////////////
        // CONNECTION //
        // /////////////
        // connection was not set in request
        if (!$this->connection) {
            throw new \Nette\InvalidArgumentException("Missing connection name.");
        }

        $connections = $this->getConnections();

        if (!$connections->has($this->connection)) {
            throw new \Nette\InvalidArgumentException(sprintf("Connection '%s' does not exist in system.", $this->connection));
        }
    }

    /**
     * Validates node name parameter.
     * 
     * @throws \Nette\InvalidArgumentException
     */
    protected function validateNode() {
        if (($this->node != IConnection::NODE_ERP) && ($this->node != IConnection::NODE_SHOP)) {
            throw new \Nette\InvalidArgumentException("Unsupported node type, expected 'erp' or 'shop'.");
        }
    }
    
    /**
     * Searches for files - depends on API request.
     * 
     */
    protected function getFiles() {
        $sourceFolder = $this->getFolder($this->connection, self::FOLDER_OUTPUT);
        
        return \Nette\Utils\Finder::findFiles($this->getAllowedFileMask())->in($sourceFolder);
    }    

    /**
     * Returns class name. 
     * 
     * @param bool $lowercase
     * @return string
     */
    protected function getPureName($lowercase = false) {
        $name = substr($this->getName(), strrpos($this->getName(), ':') + 1);

        if ($lowercase) {
            $name = strtolower($name);
        }

        return $name;
    }

    /**
     * Returns an XML response including all files list.
     * @param type $files
     */
    protected function getResponseList($files) {
        $xml = new \SimpleXMLElement('<root/>');
        $xml->addAttribute('erp', $this->erp);

        $toOrder = array();
        
        foreach ($files as $key => $file) {
            $toOrder[] = array(
                            'filename' => $file->getFilename(),
                            'time' => $file->getMTime(),
                            'size' => $file->getSize(),                           
            );
        }
        
        arsort($toOrder);
        
        foreach ($toOrder as $file) {
            $name = $file['filename'];
            $lastModified = date("d.m.y H:i:s", $file['time']);
            $size = $file['size'];

            $elem = $xml->addChild("item", $name);
            $elem->addAttribute('generated', $lastModified);
            $elem->addAttribute('size', $size);
        }

        return new XmlResponse($xml->asXML());
    }

    /**
     * Creates FileResponse to download requested file.
     * 
     * @param array $files
     * @return \Nette\Application\Responses\FileResponse
     * @throws \Nette\IOException
     */
    protected function getResponseFile($files) {
        $requestedFile = null;
        $requestedFileName = $this->fileName;

        // search for requested file in files folder
        foreach ($files as $key => $file) {
            if ($requestedFileName == $file->getFilename()) {
                $requestedFile = $file;
                break;
            }
        }

        // requested file does not exists
        if ($requestedFile === null) {
            throw new \Nette\IOException(sprintf("File '%s' does not exists.", $requestedFileName));
        }

        return new FileResponse($requestedFile->getRealPath(), $requestedFileName, 'application/octet-stream');
    }

}
