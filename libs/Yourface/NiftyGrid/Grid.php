<?php

namespace Yourface\NiftyGrid;

/**
 * NiftyGrid extensions to be used as default for all grids.
 * 
 * @author Lukas Bruha
 */
abstract class Grid extends \NiftyGrid\Grid {
    
    protected $data;

    public function __construct($data) {
        parent::__construct();
        
        $this->data = $data;
    }
    
    /**
     * Default configuration.
     * 
     * @param \Nette\Application\IPresenter $presenter
     */
    protected function configure($presenter) {
        $this->setDataSource($this->getDefaultDataSource());
        
        $this->beforeInit();
        $this->init();
        $this->afterInit();
    }
    
    protected function beforeInit() {
        
    }

    protected function afterInit() {
        
    }

    /**
     * Initis columns, buttons, actions...
     * 
     */
    abstract protected function init();
    
    /**
     * Returns default datasource to be used.
     * 
     * @return \NiftyGrid\NDataSource
     */
    protected function getDefaultDataSource() {
        return new \NiftyGrid\NDataSource($this->data);
    }
}
