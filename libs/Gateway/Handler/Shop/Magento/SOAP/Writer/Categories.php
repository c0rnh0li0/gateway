<?php

namespace Gateway\Handler\Shop\Magento\SOAP\Writer;

use Gateway\IHandler,
    Gateway\Utils,
    Gateway\DataSource\Entity\Category;

/**
 * Categories DataSource to SOAP interface handler.
 *
 * @author Lukas Bruha
 */
class Categories extends \Gateway\Handler\Shop\Magento\SOAP\Writer {

    protected $type = IHandler::TYPE_CATEGORIES;

    /**
     * Sets handler options (credentials, settings etc.) and validates them.
     * Overriden due to update expectedOptions stuff.
     * 
     * @param \Gateway\Handler\Options $options
     */
    public function __construct(\Gateway\Handler\Options $options) {
        $this->expectedOptions[] = 'assignProducts';

        parent::__construct($options);
    }

    /**
     * Magento categories information.
     * 
     * @var array 
     */
    protected $categories = array(
        'info' => array(), // existing magento structure information - Magento id is key
        'assignedProducts' => array(), // a list of currently assigned products
        'erpInfo' => array(), // existing magento structure information - erpId is key
        'rootId' => 2,
        'calls' => array(), // calls to be performed to update structure
        'assignProductsCalls' => array(), // calls to perform products assignment
        'processedCount' => 0, // number of processed categories,
        'bulkAssignmentsLimit' => 1, // number of calls per one bulk assignment
        'bulkAssignProductsCalls' => array(),
    );

    /**
     * Datasource tree converted to list.
     * 
     * @var array 
     */
    protected $dataSourceList = array();

    /**
     * Multicall switcher instead of simple calls (can help to speed up process 
     * but very buggy on Magento side - sometimes freezes).
     * 
     * @var bool
     */
    protected $useMulticall = false;

    /**
     * Inits categories structure from Magento to ease following work.
     * 
     */
    protected function init() {
        // CSV - get existing categories
        $this->readExistingCategories();

        // LIST - get datasource as list for easier access
        $this->dataSourceList = $this->dataSource->toList();

        // ASSIGNED PRODUCTS
        if ($this->options->getData('assignProducts') && count($this->categories['info'])) {
            Utils::log(\Logger\ILogger::DEBUG, sprintf("PRODUCTS ASSIGNMENT: Loading already assigned products for %s categories...", count($this->categories['info'])));

            $assignedProductsCalls = array();

            foreach (array_keys($this->categories['info']) as $id) {
                $assignedProductsCalls[$id] = array('catalog_category.assignedProducts', $id);
            }

            $assignedProducts = $this->getClient()->multiCall($assignedProductsCalls);

            // mapping categoryId => array(product_ids)
            $combined = array_combine(array_keys($assignedProductsCalls), array_values($assignedProducts));

            $this->categories['assignedProducts'] = $combined;

            Utils::log(\Logger\ILogger::DEBUG, "PRODUCTS ASSIGNMENT: Loaded %s existing assignments.", count($combined));
        }

        // DELETE MISSING CATEGORIES
        // first delete from Magento those that does not exist in XML
        $dataSourceKeys = array_keys($this->dataSourceList); // erp IDs from datasource
        $magentoKeys = array_keys($this->categories['erpInfo']); // erp IDs from Magento
        $deleteErpIds = array_diff($magentoKeys, $dataSourceKeys);

        /*
          dump($dataSourceKeys);
          dump($magentoKeys);
          dump(array_diff($magentoKeys, $dataSourceKeys));
         */

        Utils::log(\Logger\ILogger::DEBUG, "Missing categories delete...");

        if (count($deleteErpIds)) {
            $calls = array();

            foreach ($deleteErpIds as $erpId) {
                $info = $this->getByErpId($erpId);

                $calls[] = array('catalog_category.delete', $info['id']);
            }

            // perform delete by multicall
            Utils::log(sprintf("Deleting categories having erpIds '%s'.", implode(", ", $deleteErpIds)));
            $this->getClient()->multiCall($calls);
            Utils::log("Done.");
        }
    }

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
        if (!($this->dataSource instanceof \Gateway\DataSource\Categories)) {
            throw new \Nette\InvalidArgumentException(sprintf("Unsupported datasource format. Expecting '%s', '%s' given.", "\Gateway\DataSource\Customers", get_class($this->dataSource)));
        }

        Utils::log("Processing DataSource to Magento via SOAP client...");

        // SCENARIO
        // 1) extract ids from tree
        // 2) get all categories info
        // 3) make comparsion (according to erpId) and merge
        // 4) delete not existing
        // 5) process synchro for updated or new only            
        try {
            // init current magento state
            $this->init();

            // process leaves of tree structure
            foreach ($this->dataSource as $leaf) {
                // go upper in structure and prepare calls
                Utils::log(sprintf("Building calls for '%s' leaf, final path is: '%s'.", $leaf->name, $leaf->path));

                $this->processNode($leaf);
            }

            // process products by bulkAssign
            if ($this->options->getData('assignProducts')) {
                if (count($this->categories['bulkAssignProductsCalls']) && !$this->useMulticall) {
                    $this->bulkProductAssignment();
                }
            }
        } catch (\SoapFault $e) {
            Utils::log(sprintf("Error during passing via Magento SOAP API: %s (%s)", $e->getMessage(), $e->getCode()));

            throw $e;
        }

        Utils::log(sprintf("Finished with total of %s categories, %s created/updated.", count($this->dataSourceList), $this->categories['processedCount']));

        return $this->categories['processedCount'];
    }

    /**
     * Returns category according to given erp id, if exists.
     * 
     * @param int $erpId
     * @return null
     */
    protected function getByErpId($erpId) {
        if (isset($this->categories['erpInfo'][$erpId])) {
            return $this->categories['erpInfo'][$erpId];
        }

        return null;
    }

    /**
     * Processes specific category node.
     * 
     * @param \Gateway\DataSource\Entity\Category $category
     * @return int
     */
    protected function processNode($category) {
        $parentId = $this->categories['rootId'];

        // parent must be created first - go one level up in structure
        if ($category->hasParent()) {
            $parentId = $this->processNode($category->getParent());
        }

        // key for call
        //$this->connection->applyLocalizationMapping($category);
        $uniqueKey = $category->id;

        // if call not already set, make comparsion and prepare call 
        if (!array_key_exists($uniqueKey, $this->categories['calls'])) {
            //$storeView = $category->lang;

            $data = array();
            $data['name'] = $category->name;
            $data['description'] = $category->description;
            $data['is_active'] = 0;
            $data['include_in_menu'] = 0;
            $data['is_anchor'] = $category->getSpecialProperty('isAnchor') ? 1 : 0;
            $data['position'] = $category->getSpecialProperty('priority');

            $data['available_sort_by'] = 'position';
            $data['default_sort_by'] = 'position';

            $data['erpid'] = $category->id;

            // image
            if ($category->image) {
                $data['thumbnail'] = $category->image;
            }

            $array = array();

            // try to get existing category
            $magCategory = $this->getByErpId($data['erpid']);
            //$magCategory = null;

            /* dump($data['erpid']);
              dump($magCategory);
              dump($this->categories['info']);
              exit; */

            // call decision
            if ($magCategory) { // category already exists in Magento => compare and update
                $isEqual = $this->isEqual($magCategory, $category);

                // if category was changed, update it
                if (!$isEqual) {
                    $this->categories['processedCount']++;

                    $method = 'catalog_category.update';
                    $id = $magCategory['id'];

                    $array[] = $id; // id
                    $array[] = $data; // data
                    //$this->category['calls'][$uniqueKey] = $this->call('catalog_category.update', $array);
                    //$this->categories['calls'][$uniqueKey] = array('catalog_category.update', $array);
                    // default store view
                    if ($this->useMulticall) {
                        Utils::log(\Logger\ILogger::DEBUG, sprintf("Preparing call '%s' with '%s'.", $method, print_r($array, true)));

                        $calls = array();
                        $calls[] = array($mehod, $array);
                    } else {
                        Utils::log(\Logger\ILogger::DEBUG, sprintf("Calling '%s' with '%s'.", $method, print_r($array, true)));
                        $this->getClient()->call($method, $array);
                    }

                    if ($category->hasLocalizedInfo()) {
                        Utils::log(sprintf("Updating storeviews for '%s'...", $category->name));

                        foreach ($category->getLocalizedInfo() as $localized) {
                            $this->connection->applyLocalizationMapping($localized);

                            $updateData = array();

                            $updateData['name'] = $localized->name;
                            $updateData['description'] = $localized->description;
                            $updateData['is_active'] = $category->isActive ? 1 : 0;
                            $updateData['include_in_menu'] = $category->isVisible ? 1 : 0;
                            $updateData['available_sort_by'] = 'position';
                            $updateData['default_sort_by'] = 'position';

                            // image
                            if ($category->image) {
                            $updateData['thumbnail'] = $category->image;
                            }

                            /* if (!$this->isUpdated($localized, $m)) {
                              continue;
                              } */

                            $array[0] = $id;
                            $array[1] = $updateData;
                            $array[2] = $localized->lang;

                            // FIXME multicall
                            if ($this->useMulticall) {
                                Utils::log(\Logger\ILogger::DEBUG, sprintf("Preparing call '%s' with '%s'.", $method, print_r($array, true)));
                                $calls[] = array($method, $array);
                            } else {
                                Utils::log(\Logger\ILogger::DEBUG, sprintf("Calling '%s' with '%s'.", $method, print_r($array, true)));
                                $this->getClient()->call($method, $array);
                            }
                        }

                        if ($this->useMulticall) {
                            Utils::log(sprintf("Processing %s multicalls", count($calls)));
                            $this->getClient()->multicall($calls);
                        }

                        Utils::log("Done.");
                    }

                    Utils::log(\Logger\ILogger::DEBUG, "Category updated.");

                    $this->categories['calls'][$uniqueKey] = $magCategory['id'];
                }

                // FIXME return magCategory['id'] or magCategory['parent_id'] ?
                $res = $magCategory['id'];
            } else { // insert
                $this->categories['processedCount']++;
                $method = 'catalog_category.create';

                $array[0] = $parentId;
                $array[1] = $data;

                //$this->categories['calls'][$uniqueKey] = array('catalog_category.create', $array);
                // default store view
                Utils::log(\Logger\ILogger::DEBUG, sprintf("Calling '%s' with '%s'.", $method, print_r($array, true)));
                $id = $this->getClient()->call($method, $array);

                // store views
                $method = 'catalog_category.update';

                if ($category->hasLocalizedInfo()) {
                    Utils::log(sprintf("Updating storeviews for '%s'...", $category->name));

                    if ($this->useMulticall) {
                        $calls = array();
                    }

                    foreach ($category->getLocalizedInfo() as $localized) {
                        $this->connection->applyLocalizationMapping($localized);

                        $updateData = array();

                        $updateData['name'] = $localized->name;
                        $updateData['description'] = $localized->description;
                        $updateData['is_active'] = $category->isActive ? 1 : 0;
                        $updateData['include_in_menu'] = $category->isVisible ? 1 : 0;
                        $updateData['available_sort_by'] = 'position';
                        $updateData['default_sort_by'] = 'position';

                        $array[0] = $id;
                        $array[1] = $updateData;
                        $array[2] = $localized->lang;

                        // FIXME multicall sometimes throws WSDL error - bug of Soap client?
                        if ($this->useMulticall) {
                            Utils::log(\Logger\ILogger::DEBUG, sprintf("Preparing call '%s' with '%s'.", $method, print_r($array, true)));
                            $calls[] = array($method, $array);
                        } else {
                            Utils::log(\Logger\ILogger::DEBUG, sprintf("Calling '%s' with '%s'.", $method, print_r($array, true)));
                            $this->getClient()->call($method, $array);
                        }
                    }

                    if ($this->useMulticall) {
                        Utils::log(sprintf("Processing %s multicalls", count($calls)));
                        $this->getClient()->multicall($calls);
                    }

                    Utils::log("Done.");
                }

                Utils::log(\Logger\ILogger::DEBUG, "Category inserted.");

                $res = $this->categories['calls'][$uniqueKey] = $id;
            }

            // assign products to category
            if ($this->options->getData('assignProducts')) {
                $this->processProducts($res, $category);
            }
        } else {
            $res = $this->categories['calls'][$uniqueKey];
        }

        return $res;
    }

    /**
     * Processes assigned products.
     * Used only if options['assignProducts'] is set to true.
     * 
     * @param int $id
     * @param \Gateway\DataSource\Entity\Category
     */
    protected function processProducts($id, $categoryEntity) {
        $skusStr = $categoryEntity->getSpecialProperty('products');
        //$alreadyAssigned = $this->getClient()->call('catalog_category.assignedProducts', $id);

        $skus = explode(',', $skusStr);

        // DELETE PRODUCTS
        $assignedDeleteCalls = array();

        if (isset($this->categories['assignedProducts'][$id])) {
            $items = $this->categories['assignedProducts'][$id];
            
            // if already assigned products and we got also new to be assigned, we pass through
            if (count($items) && $skusStr) {
                Utils::log(\Logger\ILogger::DEBUG, sprintf("Products will be re-assigned for category '%s'.", $categoryEntity->id));
   
                foreach ($items as $item) {
                 
                    /* dump($item['sku']);
                      dump($skus);
                      dump(in_array($item['sku'], $skus));
                      echo "<br>"; */

                    // if an old assigned product is not in new assigned products list, we remove it
                    if (!in_array($item['sku'], $skus)) {
                        Utils::log(\Logger\ILogger::DEBUG, sprintf("Preparing delete of assigned product '%s' for category '%s (erpId=%s)'.", $item['sku'], $id, $categoryEntity->id));
                        $assignedDeleteCalls[] = array('catalog_category.removeProduct', array($id, $item['sku'] . ' '), 'SKU'); // NOTICE!!! space must be added due to Magento bug
                    }
                }

                if (count($assignedDeleteCalls)) {
                    Utils::log(sprintf("Deleting assigned products for '%s'...", $categoryEntity->name));
                    $this->getClient()->multiCall($assignedDeleteCalls);
                    Utils::log("Done.");
                }
            }
        }

        // INSERT PRODUCTS
        if ($skusStr) {
            $this->categories['assignProductsCalls'] = array();

            foreach ($skus as $sku) {
                //echo $categoryEntity->id . ' - ' . $categoryEntity->name . ': ' . $sku . "<br />";
                $data = array('categoryId' => $id, 'product' => $sku . ' ');

                if ($this->useMulticall) {
                    $key = $id . '_' . $sku;
                    Utils::log(\Logger\ILogger::DEBUG, sprintf("Preparing product '%s' to be assigned to category '%s' (erpId=%s).", $sku, $categoryEntity->name, $categoryEntity->id));
                    $this->categories['assignProductsCalls'][$key] = array('catalog_category.assignProduct', $data);
                } else {
                    //Utils::log("Assigning product to category: %s to '%s'", $sku, $categoryEntity->name);
                    // skip if product does not exists or any other error
                    /*                     * try {
                      $this->getClient()->call('catalog_category.assignProduct', $data);
                      Utils::log("Done.");
                      } catch (\SoapFault $e) {
                      Utils::log(\Logger\ILogger::ERROR, sprintf("Magento SOAP API error: %s (%s)", $e->getMessage(), $e->getCode()));
                      Utils::log("Skipping product %s assignment...", $sku);
                      } */
                }
            }

            // MULTICALL ASSIGNMENT
            if ($this->useMulticall) {
                Utils::log("Assigning products to categories via multicall...");
                $this->getClient()->multiCall($this->categories['assignProductsCalls']);
                Utils::log("Products assigned.");
            } else {
                // BULK ASSIGNMENT
                Utils::log(\Logger\ILogger::DEBUG, sprintf("Preparing bulkAssign of '%s' products for category '%s'.", $skusStr, $categoryEntity->name));

                $this->categories['bulkAssignProductsCalls'][] = array($id, $skus);
            }
        }
    }

    protected function bulkProductAssignment() {
        $calls = array();
        $step = 1;

        // module counting
        $mod = $this->categories['bulkAssignmentsLimit'];

        if (count($this->categories['bulkAssignProductsCalls']) < $mod) {
            $mod = count($this->categories['bulkAssignProductsCalls']);
        }

        // process itself
        foreach ($this->categories['bulkAssignProductsCalls'] as $call) {
            $calls[] = $call;

            // LIMITATION
            if ($step++ % $mod == 0) {
                try {
                    Utils::log(\Logger\ILogger::DEBUG, "Performing bulk products assignments...");
                    Utils::log(\Logger\ILogger::DEBUG, print_r($calls, true));
                    $this->getClient()->call('catalog_category.bulkAssign', array($calls));
                    Utils::log(\Logger\ILogger::DEBUG, "Done.");

                    // re-init calls load
                    $calls = array();
                } catch (\SoapFaultException $e) {
                    Utils::log(\Logger\ILogger::ERROR, sprintf("Magento SOAP API error: %s (%s)", $e->getMessage(), $e->getCode()));
                    Utils::log("Skipping product %s assignments...", print_r($calls, true));
                }
            }
        }
    }

    /**
     * Process CSV.
     * 
     * FIXME do as external, same method used in Products/Categories handler
     * 
     * @return array
     * @throws \Nette\IOException
     * @throws \Nette\MemberAccessException
     */
    protected function readExistingCategories() {
        $domain = $this->options->getData('domain');
        //$domain = "http://magento.p168415.webspaceconfig.de";
        $url = $domain . '/listcategories.php';

        //$url = APP_DIR . '/../temp/categories.txt';

        Utils::log(\Logger\ILogger::DEBUG, sprintf("Calling '%s' to get existing categories CSV file...", $url));
        $csv = @file_get_contents($url);

        if (!$csv) {
            Utils::log(\Logger\ILogger::DEBUG, "No CSV file exists.");
            throw new \Nette\IOException(sprintf("Cannot read CSV file from '%s'.", $url));
        }

        Utils::log(\Logger\ILogger::DEBUG, "Parsing CSV...");

        //$lines = explode("\n", $csv);
        $lines = str_getcsv($csv, "\n");

        $delimiter = ",";
        $colNames = str_getcsv(array_shift($lines), $delimiter);
        $res = array();

        foreach ($lines as $line) {
            // Skip the empty line
            if (empty($line)) {
                continue;
            }

            $fields = str_getcsv($line, $delimiter);
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

            $this->categories['info'][$row['id']] = $row;
            $this->categories['erpInfo'][$row['erpid']] = $row;
        }

        Utils::log(\Logger\ILogger::DEBUG, "Done.");

        return $res;
    }

    /**
     * Compares CSV and DataSource categories.
     * 
     * @param array $magCategory
     * @param Category $category
     * @return boolean
     * @throws \Nette\MemberAccessException
     */
    protected function isEqual($magCategory, $category) {
        Utils::log(\Logger\ILogger::DEBUG, sprintf("Checking category '%s' having erpId ='%s' for changes.", $category->name, $category->id));
        
		//Utils::log(\Logger\ILogger::DEBUG, sprintf("Dump magCategory: '%s' " , print_r($magCategory, true)));
		//Utils::log(\Logger\ILogger::DEBUG, sprintf("Dump category: '%s' ", print_r($category, true)));
        
		
        if (!isset($magCategory['name_en']) && !isset($magCategory['name_de'])) {
            throw new \Nette\MemberAccessException("Invalid existing category format.");
        }

        // PREPARE AND MAPPING - lets create magento category as entity object to apply mapping
        $compCategory = new Category();
        $compCategory->id = $magCategory['erpid'];
        
		//NOT PROPERLY WORKING - see comparison below
        //$compCategory->isActive = $magCategory['is_active'] > 0 ? true : false;
        //$compCategory->isVisible = $magCategory['include_in_navigation_menu'] > 0  ? true : false; 
        
        $compCategory->addSpecialProperty('priority', $magCategory['position']);

        $compCategoryLocalizedEn = new Category\LocalizedInfo('en');
        $compCategoryLocalizedEn->name = $magCategory['name_en'];

        $compCategoryLocalizedDe = new Category\LocalizedInfo('de');
        $compCategoryLocalizedDe->name = $magCategory['name_de'];

        $compCategory->addLocalizedInfo($compCategoryLocalizedEn);
        $compCategory->addLocalizedInfo($compCategoryLocalizedDe);

        // apply rules to re-map language
        $this->connection->applyLocalizationMapping($compCategoryLocalizedEn, true);
        $this->connection->applyLocalizationMapping($compCategoryLocalizedDe, true);

        // COMPARSION itself
        //dump($compCategory);
        $isEqual = true;

        // priority changed
        if ($compCategory->getSpecialProperty('priority') != $category->getSpecialProperty('priority')) {
            $isEqual = false;

            Utils::log(\Logger\ILogger::DEBUG, sprintf("Position was changed: %s => %s", $compCategory->getSpecialProperty('priority'), $category->getSpecialProperty('priority')));
        }
        
        // isActive changed
        if ($magCategory['is_active'] != $category->isActive) {
            $isEqual = false;

            Utils::log(\Logger\ILogger::DEBUG, sprintf("IsActive was changed: %s => %s", $magCategory['is_active'], $category->isActive));
        }

        // isVisible changed
        if ($magCategory['include_in_navigation_menu'] != $category->isVisible) {
            $isEqual = false;

            Utils::log(\Logger\ILogger::DEBUG, sprintf("IsVisible was changed: %s => %s", $magCategory['include_in_navigation_menu'], $category->isVisible));
        }

        // thumbnail changed
        if ($magCategory['thumbnail'] != $category->image) {
            $isEqual = false;

            Utils::log(\Logger\ILogger::DEBUG, sprintf("Thumbnail was changed: %s => %s", $magCategory['thumbnail'], $category->image));
        }		
		
        // localized
        foreach ($category->localizedInfo as $localized) {
            foreach ($compCategory->localizedInfo as $compLocalized) {
                // compare on specific localization
                if ((int) $compLocalized->lang == (int) $localized->lang) {
                    if (trim($compLocalized->name) != trim($localized->name)) {
                        $isEqual = false;

                        Utils::log(\Logger\ILogger::DEBUG, sprintf("Category name was changed: '%s' => '%s'", $compLocalized->name, $localized->name));
                    }

                    // compared, we can skip rest
                    break;
                }
            }
        }

        // log only
        $msg = "Category '%s' having erpId ='%s' WILL NOT BE updated.";

        if (!$isEqual) {
            $msg = "Category '%s' having erpId ='%s' WILL BE updated.";
        }

        Utils::log(\Logger\ILogger::DEBUG, sprintf($msg, $category->name, $category->id));

        return $isEqual;
    }

}