<?php
namespace Gateway\DataSource\Entity;

/**
 * Product interface.
 * 
 * @author Lukas Bruha
 */
interface IProduct {
    
    const TYPE_SIMPLE = 'simple';
    const TYPE_CONFIGURABLE = 'configurable';
    const TYPE_BUNDLE = 'bundle';
       
}
