<?php
namespace Gateway;

/**
 * Connection entity interface.
 * 
 * @author Lukas Bruha
 */
interface IConnection {
    
    const NODE_ERP = 'erp';
    const NODE_SHOP = 'shop';
    
    const STREAM_READER = 'reader';
    const STREAM_WRITER = 'writer';
    
}

