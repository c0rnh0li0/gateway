<?php

namespace Gateway\Handler\Shop\Magento\SOAP\Writer\Products;

use Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Product\IImage;

/**
 * Products' images DataSource to SOAP interface handler. 
 *
 * @author Lukas Bruha
 */
class Images extends \Gateway\Handler\Shop\Magento\SOAP\Writer {

    protected $type = IHandler::TYPE_PRODUCTS_IMAGES;

    /**
     * Number of processed images.
     * 
     * @var int
     */        
    protected $processed = 0;
    
    /**
     * Converts DataSource to SOAP.
     * 
     * @return int Number of processed items.
     */
    public function process() {
        Utils::log(">>> " . get_class($this) . " <<<");

        // FIXME check when setting datasource
        if (!$this->dataSource) {
            Utils::log("No datasource given. Skipping...");
            return;
        }

        // invalid datasource format
        if (!($this->dataSource instanceof \Gateway\DataSource\Products)) {
            throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Products", get_class($this->dataSource)));
        }

        $calls = array();
        
        Utils::log("Processing DataSource to Magento via SOAP client...");

        foreach ($this->dataSource as $product) {
            //Debugger::dump($product);
            
            if ($product->hasImages()) {
                Utils::log(sprintf("Preparing images for product '%s'", $product->sku));
                
                // $images[<OPERATION>][<NAME>] = $imgArray;
                $images = array();
                
                // 1) PRODUCT IMAGES TRANSFORMATION
                foreach ($product->getImages() as $type => $image) {
                    
                    // gallery image we simply pass
                    if ($type == IImage::TYPE_GALLERY) {
                        foreach ($image as $gImage) {
                            $name = $gImage->getName();
                            $operation = $gImage->getSpecialProperty('operation');
                                
                            // for delete operation we do not need any source, just name
                            if ($operation == 'delete') {
                                $images[$operation][$name] = $this->createImage($gImage, true);
                            } else {                            
                                Utils::log(sprintf("Getting image %s", $gImage->getPath()));

                                // add only if file exists
                                if (file_exists($gImage->getPath())) {
                                    $images[$operation][$name] = $this->createImage($gImage);
                                    Utils::log("Done.");
                                } else {
                                    $msg = sprintf("Image '%s' does not exists, skipping...", $gImage->getPath());

                                    Utils::log(\Logger\ILogger::WARNING, $msg);
                                    //throw new \Nette\IOException($msg);
                                }
                            }
                        }
                    } else { // other we create
                        $name = $image->getName();
                        $operation = $image->getSpecialProperty('operation');                        
                        
                        // for delete operation we do not need any source, just name
                        if ($operation == 'delete') {
                            $images[$operation][$name] = $this->createImage($image, true);
                        } else {
                            $iType = $this->convertImageType($image);
                            
                            // add only if file exists
                            Utils::log(sprintf("Getting image %s", $image->getPath()));

                            if (file_exists($image->getPath())) {
                                // images does not already exists => create it
                                if (!isset($images[$operation][$name])) {

                                    $imgArr = $this->createImage($image);

                                    $images[$operation][$name] = $imgArr;
                                    Utils::log("Done.");
                                } else {
                                    Utils::log("Already exists. Skipping...");                                
                                }

                                // not specified iType means standard gallery image
                                if ($iType) {
                                    $images[$operation][$name]['types'][] = $iType;
                                }
                            } else {
                                $msg = sprintf("Image '%s' does not exists, skipping...", $image->getPath());

                                Utils::log(\Logger\ILogger::WARNING, $msg);
                                //throw new \Nette\IOException($msg);
                            }
                        }
                    }
                }
                
                // 2) SOAP CALLS PREPARING
                if (count($images)) {
                    Utils::log(sprintf("Preparing SOAP calls for '%s' image(s) of product...", count($images)));

                    foreach ($images as $operation => $items) {
                        $sku = $product->sku . " "; // hack to allow numerical product SKU - Magento SOAP API bug

                        // go through all operations
                        foreach ($items as $name => $image) {
                            $call = array();

                            // for deleting we have only name, no other attributes
                            if ($operation == 'delete') {
                                Utils::log(sprintf("'%s': '%s'", $operation, $name));
                            } else {
                                Utils::log(sprintf("'%s': '%s' (types: '%s')", $operation, $name, count($image['types']) ? implode(", ", $image['types']) : "gallery"));
                            }
                            
                            switch ($operation) {
                                case 'update':
                                    $call['onSuccess'] = array('product_media.update', array($sku, $this->tokenizeName($name), $image));
                                    $call['onFail'] = array('product_media.create', array($sku, $image));
                                    break;
                                case 'delete':
                                    $call['onSuccess'] = array('product_media.remove', array($sku, $this->tokenizeName($name)));
                                    break;
                                default:
                                    $call['onSuccess'] = array('product_media.create', array($sku, $image));
                                    break;
                            }

                            $calls[] = $call;
                        }
                    }

                    /*if (count($images) > 1) {
                    dump($product);
                    dump($images);
                    exit;
                    }*/

                    Utils::log("Prepared to call via SOAP...");
                } else {
                    Utils::log("No images to import for product.");                    
                }
            }
        }

        // 3) SOAP MULTICALL - PASSING IMAGES
        if (count($calls)) {
                
            /*dump($calls[0][1]);
            exit;*/
            
                Utils::log("EXECUTION: Executing SOAP API...");

                // MULTICALL
                //$res = $this->getClient()->multiCall($calls);
                //dump($res);
                
                // SIMPLE CALLS
                foreach ($calls as $call) {                    
                    try {
                        $method = $call['onSuccess'];
                        
                        Utils::log(\Logger\ILogger::DEBUG, sprintf("Calling method '%s' on product '%s'", $method[0], $method[1][0]));

                        $logMethod = $method;
                        
                        // remove binary content from log
                        \Yourface\Utils\Arrays::searchAndRemove($logMethod, 'content', true);
                        Utils::log(\Logger\ILogger::DEBUG, print_r($logMethod, TRUE));

                        $this->getClient()->call($method[0], $method[1]);
                        
                        $this->processed++;
                    } catch (\SoapFault $e) {
                        Utils::log(\Logger\ILogger::ERROR, sprintf("Magento SOAP API error: %s (%s)", $e->getMessage(), $e->getCode()));
                        //Utils::log(\Logger\ILogger::DEBUG, sprintf("SOAP response: %s \n", $this->getClient()->getLastResponse()));
                        
                        // FIMXE too ugly (no code is returned from SOAP API) but requested from customer: 
                        // if product image does not exists during update, we execute insert
                        if (strpos($e->getMessage(), "Requested image not exists in product") !== false) {
                            if (isset($call['onFail'])) {
                                Utils::log(\Logger\ILogger::DEBUG, "Trying to insert missing image instead of update...");
                            
                                $method = $call['onFail'];
                                $logMethod = $call['onFail'];
                                
                                try {
                                    // remove binary content from log
                                    \Yourface\Utils\Arrays::searchAndRemove($logMethod, 'content', true);                    
                                    Utils::log(\Logger\ILogger::DEBUG, print_r($logMethod, TRUE));
                                    
                                    $this->getClient()->call($method[0], $method[1]);
                                    Utils::log(\Logger\ILogger::DEBUG, "Inserted successfully.");

                                    $this->processed++;
                                } catch (\SoapFault $e) {
                                    Utils::log(\Logger\ILogger::ERROR, sprintf("Magento SOAP API error: %s (%s)", $e->getMessage(), $e->getCode()));
                                    Utils::log(\Logger\ILogger::ERROR, "Neither updated, nor inserted.");

                                    //throw $e;
                                }
                            } else {
                                 Utils::log(\Logger\ILogger::DEBUG, "Action skipped.");
                            }
                        } /*else {
                            //throw $e;
                        }*/
                    }
                }
                
                Utils::log("Images imported successfully.");
        }
        
        Utils::log(sprintf("Finished with total of %s products and %s images.", $this->dataSource->count(), $this->processed));
        
        return $this->processed;
    }

    /** 
     * FIX Magento expects also subfolders in name (first two characters)
     * so instead of 'some_picture' it expects '/s/o/some_picture'
     * 
     * @return string
     */
    protected function tokenizeName($name) {
        return "/" . $name[0] . "/" . $name[1] . "/" . $name;
    }
    
    /**
     * Converts image file into array.
     * 
     * @param \SplFileInfo $image
     * @param bool $simple
     */        
    protected function createImage($image, $simple = false) {        
        // extract extension from file to have name with no extension - it is 
        // added automatically
        $name = preg_replace("/\\.[^.\\s]{3,4}$/", "", $image->getName());
        
        // simple returns name only
        if ($simple) {
            $imgArr = array('file' => array('name' => $name));
        } else {
            $info = getimagesize($image->getPath());
            $mime = image_type_to_mime_type($info[2]);
            $binary = base64_encode(file_get_contents($image->getPath()));

            //$mime = 'image/jpeg';
            //$binary = 'asdf';
            $imgArr = array(
                            'file' => array(
                                'name' => $name,
                                'content' => $binary,
                                'mime'    => $mime
                            ),
                            'label'    => $image->getLabel(),
                            'position' => $image->getPriority(),
                            'types'    => array(),
                            'exclude'  => !$image->isVisible()
                        );
        }
        
        return $imgArr;
    }
    
    /**
     * Returns image type being used in Magento.
     * 
     * @param \SplFileInfo $image
     */        
    protected function convertImageType($image) {
        $type = $image->getType();
        
        switch ($type) {
            case IImage::TYPE_PREVIEW_MEDIUM:
                return 'small_image';
                break;
            case IImage::TYPE_PREVIEW;
                return 'image';
                break;
            case IImage::TYPE_PREVIEW_SMALL;
                return 'thumbnail';
                break;
            default:
                Utils::log(\Logger\ILogger::ERROR, sprintf("No image type support for '%s'.", (string) $image));
                return null;
                break;
                 
        }
    }
}

