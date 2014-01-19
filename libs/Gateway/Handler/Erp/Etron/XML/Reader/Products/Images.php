<?php

namespace Gateway\Handler\Erp\Etron\XML\Reader\Products;

use Gateway\Handler\Erp\Etron\XML\Reader\Products,
    Gateway\IHandler,
    Gateway\Utils,
    Nette\Diagnostics\Debugger;

/**
 * Products images XML to DataSource handler.
 *
 * @author Lukas Bruha
 */
class Images extends Products {

    protected $type = IHandler::TYPE_PRODUCTS_IMAGES;
    
    protected $debug = array(
                        'limit' => false,
    );
    
    /**
     * Parse given XML file and creates product structure.
     * 
     * @param \SimpleXml_Element $xml
     */
    protected function processXml($xml) {
        Utils::log("Processing products (images only) XML input...");
 
        $products = new \Gateway\DataSource\Products();
        
        // path where images can be found
        $imagesStorage = $this->getPath('files');

        // if not exits, we return empty datasource
        // because no images can be processed
        if (is_dir($imagesStorage)) {
            $step = 0;

            foreach ($xml->products as $xmlProduct) {
                // product description
                $xmlProductDescriptionPattern = '//products_description[@products_id=' . $xmlProduct['products_id'] . ']';
                $xmlProductDescription = $xml->xpath($xmlProductDescriptionPattern);
                $xmlProductDescription = current($xmlProductDescription);

                // 0 = simple, 1 = configurable
                $typeKey = (int) $xmlProduct['products_master_flag'];
                $isBundle = (isset($xmlProduct['products_bundle_flag']) && ((int) $xmlProduct['products_bundle_flag'])) ? true : false;

                //$isMaster = isset($xmlProduct['products_master_model']) && (int) $xmlProduct['products_master_model'] ? true : false;
                $attributeSet = "default"; // FIXME put this information into XML as eg. <attributes products_id="13352" set="tshirt">

                $product = $this->createProduct($typeKey, $isBundle);
                //$product->id = (int) $xmlProduct['products_id'];
                $product->sku = (string) $xmlProduct['products_model'];

                // IMAGES
                // images names must be converted from IMAGE_01.jpg[;IMAGE_0n.jpg] to array
                if (!isset($xmlProduct['products_image'])) {
                    Utils::log(\Logger\ILogger::WARNING, sprintf("Invalid products XML element - missing products_image attribute for product SKU of '%s'", $product->sku));
                    continue;
                }
                $xmlImagesNames = (string) $xmlProduct['products_image'];

                // if images found, we pass them
                if ($xmlImagesNames) {
                    $xmlImagesNames = explode(";", $xmlImagesNames);

                    //$xmlImagesNames = array('+123.jpg', '-456.jpg', '789.jpg');
                    $imagesCount = 0;
                    
                    foreach ($xmlImagesNames as $index => $image) {                        
                        $oImage = new \Gateway\DataSource\Entity\Product\Image($image, \Gateway\DataSource\Entity\Product\IImage::TYPE_GALLERY);
                        
                        // extract CRUD from name: +<name> = force update, -<name> = delete, <name> = create
                        if (\Nette\Utils\Strings::startsWith($image, "+")) {
                            $oImage->addSpecialProperty('operation', 'update');
                        } elseif (\Nette\Utils\Strings::startsWith($image, "-")) {
                            $oImage->addSpecialProperty('operation', 'delete');
                        } else {
                            $oImage->addSpecialProperty('operation', 'create');                            
                        }
                        
                        // remove prefix + or - from image name
                        $image = preg_replace("/^[+|-]*/", "", $image);
                        $oImage->name = $product->sku . '_' . $image;
                        $oImage->path = $this->getPath('files') . DIRECTORY_SEPARATOR . $image;
                            
                        // always the first image is thumbnail, small etc. in XML
                        if ($index == 0) {
                            $oImage = clone $oImage;
                            $oImage->type = \Gateway\DataSource\Entity\Product\IImage::TYPE_PREVIEW;
                            $product->addImage($oImage);
                            
                            $oImage = clone $oImage;
                            $oImage->type = \Gateway\DataSource\Entity\Product\IImage::TYPE_PREVIEW_MEDIUM;
                            $product->addImage($oImage);
                            
                            $oImage = clone $oImage;
                            $oImage->type = \Gateway\DataSource\Entity\Product\IImage::TYPE_PREVIEW_SMALL;
                            $product->addImage($oImage);
                            
                            $imagesCount++;
                        } else {
                            // the rest is gallery type
                            $oImage->priority = $index;
                            
                            $product->addImage($oImage);
                            $imagesCount++;
                        }
                    }

                    /*dump($product->getImages());
                    exit;*/
                    Utils::log(sprintf("Product '%s' has %s image(s).", $product->sku, $imagesCount));
                    
                    // add only those with images to collection
                    // to lowerize data transfer
                    $products->add($product);

                    // debug limit
                    if ($this->debug['limit'] && ($step++ == $this->debug['limit'])) {
                        break;
                    }                

                }
            }

            Utils::log("%s products with images has been found.", $products->count());
            Utils::log("Products DataSource (images only) is prepared.");
        } else {            
            $msg = sprintf("Products images storage was not found in '%s'. It seeems no images have been uploaded yet for connection '%s'.", $imagesStorage, $this->connection->name);
            Utils::log(\Logger\ILogger::ERROR, $msg);
            
            throw new \Nette\IOException($msg);
        }

        return $products;
    }
    
    /**
     * Returns input path.
     * 
     * FIXME put to parent handler
     * 
     * @return type
     * @throws \Nette\InvalidArgumentException
     */
    protected function getPath($type = false) {
        // configuration loading
        $params = \Nette\Environment::getContext()->params['gateway'];
        $root = realpath($params['storage']['root']);

        if (!isset($params['storage']['etron'])) {
            throw new \Nette\InvalidArgumentException('Etron configuration not found in config.neon[params/gateway/storage/etron]');
        }

        $path = sprintf($root . $params['storage']['etron']['inputFolderMask'], $this->connection->name);
        
        if ($type) {
            $path .= DIRECTORY_SEPARATOR . $type;
        }
        
        return $path;
    }

}