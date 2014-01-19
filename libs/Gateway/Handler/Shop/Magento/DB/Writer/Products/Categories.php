<?php

namespace Gateway\Handler\Shop\Magento\DB\Writer\Products;

use Gateway\IHandler,
    Gateway\Utils;

/**
 * Categories DataSource to SOAP interface handler.
 *
 * SCENARIO
 * 1. get category erpIds and IDs map
 * 2. get product IDs of all loaded products according to their SKUs
 * ---
 * 3. delete all categories of this product
 * 4. get category IDs from CSV according to categoryErpIds 
 * 5. insert new product categories relations into DB
 *             
 * @author Lukas Bruha
 */
class Categories extends \Gateway\Handler\Shop\Magento\DB\Writer {

    protected $type = IHandler::TYPE_PRODUCTS_CATEGORIES;
    
    protected $map = array(
                        'categories' => array(), // erpId => magentoId
                        'products' => array(), // sku => id
    );
    

    /**
     * Converts XML to DataSource.
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

        Utils::log("Processing DataSource to Magento via direct DB access...");
        
        // INIT
        // load erpId => magentoId map
        $this->readExistingCategories();
        
        // load sku => id map
        $this->readProductsIds();

        $processed = 0;
        $errors = 0;
        
        // WORK
        foreach ($this->getDataSource() as $sku => $product) {
            $this->getDb()->beginTransaction();
        
            try {
                $categoryErpIds = $product->getSpecialProperty('categoryErpIds');

                // delete from catalog_category_product where product_id = <ENTITY_ID>; 
                $mProductId = $this->map['products'][$sku];
                $this->getDb()->table('catalog_category_product')->where(array('product_id' => $mProductId))->delete();

                // if new product categories exists, we insert them 
                if ($categoryErpIds && count($categoryErpIds)) {
                    Utils::log("Product '%s' has such ERP IDs categories to assign: %s", $sku, implode(", ", $categoryErpIds));

                    // insert into catalog_category_product (category_id, product_id, position)  values (<CATEGORY_ID>, <ENTITY_ID>, 1) 
                    $data = array();

                    // prepare data to be inserted
                    foreach ($categoryErpIds as $categoryErpId) {
                        if (!isset($this->map['categories'][$categoryErpId])) {
                            Utils::log(\Logger\ILogger::WARNING, sprintf("Category having erpId = %s does not exists in Magento. Skipping...", $categoryErpId));
                            continue;
                        }

                        $mCategoryId = $this->map['categories'][$categoryErpId];

                        $data[] = array(
                                    'category_id' => $mCategoryId,
                                    'product_id' => $mProductId,
                                    'position' => 1,
                                );
                    }

                    // multiinsert per product
                    $this->getDb()->table('catalog_category_product')->insert($data);

                    $processed++;
                } else {
                    Utils::log("Product '%s' has no categories to assign.", $sku);
                }
                
                $this->getDb()->commit();
            } catch (\PDOException $e) {
                $this->getDb()->rollback();
                $errors++;
                
                Utils::log(\Logger\ILogger::WARNING, sprintf("Product %s and categories assignment error: %s", $sku, $e->getMessage()));
            }
        }
        
        // errors could raised
        if ($errors) {
            Utils::log(\Logger\ILogger::WARNING, sprintf("There were %s database errors during process.", $errors));
        }
        
        Utils::log(sprintf("Finished with total of %s created/updated.", $processed));

        return $processed;
    }
    
    /**
     * Process CSV.
     * 
     * FIXME do as external, same method used in Categories handler
     * 
     * @return array
     * @throws \Nette\IOException
     * @throws \Nette\MemberAccessException
     */
    protected function readExistingCategories() {
        $domain = $this->options->getData('domain');
        $url = $domain . '/listcategories.php';

        Utils::log(\Logger\ILogger::DEBUG, sprintf("Calling '%s' to get existing categories CSV file...", $url));
        $csv = @file_get_contents($url);

        if (!$csv) {
            Utils::log(\Logger\ILogger::DEBUG, "No CSV file exists.");
            throw new \Nette\IOException(sprintf("Cannot read CSV file from '%s'.", $url));
        }

        Utils::log(\Logger\ILogger::DEBUG, "Loading categories erpId => id map...");
        Utils::log(\Logger\ILogger::DEBUG, "Parsing CSV...");

        //$lines = explode("\n", $csv);
        $lines = str_getcsv($csv, "\n");

        $delimiter = ",";
        $colNames = str_getcsv(array_shift($lines), $delimiter);

        foreach ($lines as $line) {
        
            // Skip the empty line
            if (empty($line)) {
                continue;
            }

            $fields = explode($delimiter, $line);
            $row = array();

            foreach ($colNames as $key => $name) {
                $field = trim($fields[$key], "\"");

                $row[$name] = $field;
            }

            // missing erpid column
            if (!array_key_exists('erpid', $row)) {
                $msg = sprintf("Bad Magento categories configuration - missing 'erpid' attribute for category.", $info['name_en']);

                Utils::log(\Logger\ILogger::ERROR, $msg);
                throw new \Nette\MemberAccessException($msg);
            }

            // missing erpid value - skip
            if (!$row['erpid']) {
                continue;
            }

            $this->map['categories'][$row['erpid']] = $row['id'];
        }

        Utils::log(\Logger\ILogger::DEBUG, "%s products loaded to map.", count($this->map['categories']));
    }

    /**
     * Loads all products according to their SKUs into map.
     * 
     */
    protected function readProductsIds() {
        Utils::log(\Logger\ILogger::DEBUG, "Loading products sku => id map...");

        $skus = array_keys($this->getDataSource()->getData());
        $where = array('sku' => $skus);
        
        $products = $this->getDb()->table('catalog_product_entity')->select('entity_id, sku')->where($where);
        
        foreach ($products as $product) {
            $this->map['products'][$product['sku']] = $product['entity_id'];
        }
        
        Utils::log(\Logger\ILogger::DEBUG, "%s products loaded to map.", count($this->map['products']));
    }
}

