<?php

namespace AdminModule;

use Nette\Application\Routers\Route,
    Nette\Application\Routers\RouteList;

/**
 * Admin module routing.
 * 
 * @author Lukas Bruha
 */   
class Routes extends \Nette\Object {
    
    /**
     * Returns a list of routes for admin.
     * 
     * @var \Nette\Application\Routers\RouteList
     */       
    public static function getList() {
        $adminRouter = new RouteList('Admin');
        $adminRouter[] = new Route('admin/<presenter>/<action>', 'Default:default');
       
        return $adminRouter;
    }
    
} 