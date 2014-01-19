<?php

namespace ApiModule;

use 
    Yourface\Application\Routers\RestRoute,
    Nette\Application\Routers\Route,
    Nette\Application\Routers\RouteList;

/**
 * API routes.
 * 
 * @author Lukas Bruha
 */
class Routes extends \Nette\Object {
    
    protected static $root = 'api/rest/1.0/';
    
    /**
     * Allowed resources for REST API.
     * 
     * @var array
     */    
    protected static $resources = array(
                                    'Products' => 'products', 
                                    'Customers' => 'customers', 
                                    'Orders' => 'orders', 
                                    'Logs' => 'logs', 
                                    'ProductsImages' => 'productsimages', 
                                    'Files' => 'files',
                                    'Categories' => 'categories',
                                    'Schedules' => 'schedules',
                                    'ProductsCategories' => 'productscategories',
                                    'Stock' => 'stock',
                                    );
    
    /**
     * Specifies routes for API.
     * 
     * @return \Yourface\Application\Routers\RestRoute
     */
    public static function getList() {
        $apiRouter = new RouteList('Api');
 
        // for every resource we defined route
        foreach (self::$resources as $camelPresenter => $presenter) {
            // PUT 
            // not implemented - does nothing
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>/put', $camelPresenter . ':put');
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>', $camelPresenter . ':put', RestRoute::METHOD_PUT);

            // POST
            // uploads file or posts allowed data 
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>/post[/<node>]', $camelPresenter . ':post');
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>/post', $camelPresenter . ':post');
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>[/<node>]', $camelPresenter . ':post', RestRoute::METHOD_POST);
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>', $camelPresenter . ':post', RestRoute::METHOD_POST);

            // DELETE
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>/<fileName>/delete', $camelPresenter . ':delete');
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>/<fileName>', $camelPresenter . ':delete', RestRoute::METHOD_DELETE);

            // GET
            // updated files list overview
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>[/get]', $camelPresenter . ':get', RestRoute::METHOD_GET);
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>', $camelPresenter . ':get', RestRoute::METHOD_GET);

            // specific file download
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>/<fileName>[/get]', $camelPresenter . ':get');
            $apiRouter[] = new RestRoute(self::$root . $presenter . '/<connection>/<fileName>', $camelPresenter . ':get', RestRoute::METHOD_GET);
        
        }

        // API list
        $apiRouter[] = new RestRoute(self::$root . '<presenter>', 'Default:default', RestRoute::METHOD_GET);
      
        $apiRouter[] = new RestRoute('api[/rest][/1.0]', 'Default:default');
      
        return $apiRouter;
    }
    
} 