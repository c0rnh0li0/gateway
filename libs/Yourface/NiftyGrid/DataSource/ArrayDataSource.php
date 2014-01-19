<?php

namespace Yourface\NiftyGrid;

use NiftyGrid\FilterCondition,
    NiftyGrid\IDataSource;


/**
 * Array datasource for NiftyGrid.
 * 
 * @author Lukas Bruha
 */
class ArrayDataSource implements IDataSource {

    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function getPrimaryKey() {
        return 'id';
    }

    public function getCount($column = "*") {
        return count($this->data);
    }

    public function orderData($by, $way) {
        
    }

    public function limitData($limit, $offset) {
        
    }

    public function filterData(array $filters) {
        /* foreach($filters as $filter){
          if($filter["type"] == FilterCondition::WHERE){
          $column = $filter["column"];
          $value = $filter["value"];
          if(!empty($filter["columnFunction"])){
          $column = $filter["columnFunction"]."(".$filter["column"].")";
          }
          $column .= $filter["cond"];
          if(!empty($filter["valueFunction"])){
          $column .= $filter["valueFunction"]."(?)";
          }
          $this->data->where($column, $value);
          }
          } */
    }

}
