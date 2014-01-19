<?php

namespace AdminModule\Component;

use \Yourface\NiftyGrid\Grid,   
    \Nette\Utils\Html;

/**
 * Logs overview.
 *
 * @author Lukas Bruha
 */
class LogsGrid extends Grid {
    
    /**
     * Grid init.
     * 
     */
    protected function init() {
        $this->enableSorting = false;
        
        // columns
        $this->addColumn('name', 'Filename');
        $this->addColumn('updated_at', 'Updated at')->setRenderer(function($row) {
                            return Html::el('span')->setTitle($row->updated_at)->setText(\Yourface\Utils\Helpers::ago($row->updated_at, 43200));
                        });
        $this->addColumn('size', 'Size');

        // buttons
        $self = $this;

        $this->addButton("show")
          ->setAjax(false)
          ->setText("show")
          ->setLabel("loads file to browser")
          ->setLink(function($row) use ($self) {
                return $self->presenter->link("detail", $row['id']);
          });
        
        $this->addButton("download")
          ->setAjax(false)
          ->setText("download")
          ->setLabel("downloads file")
          ->setLink(function($row) use ($self) {
                return $self->link("open", $row['id'], $row['name']);
          }); 
    }

    /**
     * Deletes connection.
     * 
     * @param string $realpath
     * @param string $name
     */
    public function handleOpen($realpath, $name) {
       // $this->presenter->sendResponse(new \Nette\Application\Responses\TextResponse(file_get_contents($realpath)));
        
       $this->presenter->sendResponse(new \Nette\Application\Responses\FileResponse($realpath, $name, 'text/plain'));
       $this->presenter->terminate();
    }
    
    /**
     * Re-set default datasource to array.
     * 
     * @return \Yourface\NiftyGrid\ArrayDataSource
     */
    protected function getDefaultDataSource() {
        return new \Yourface\NiftyGrid\ArrayDataSource($this->data);
    }

}