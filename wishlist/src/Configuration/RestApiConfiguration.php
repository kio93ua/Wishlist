<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\DependencyInjection\Container;
use Dits\CaW\Rest\CompareClearRoute;
use Dits\CaW\Rest\CompareProductDeleteRoute;
use Dits\CaW\Rest\CompareProductPostRoute;
use Dits\CaW\Rest\CompareRoute;
use Dits\CaW\Rest\CompareTableRoute;
use Dits\CaW\Rest\RestApi;
use Dits\CaW\Rest\WishlistDeleteRoute;
use Dits\CaW\Rest\WishlistPatchRoute;
use Dits\CaW\Rest\WishlistPostRoute;
use Dits\CaW\Rest\WishlistProductDeleteRoute;
use Dits\CaW\Rest\WishlistProductPostRoute;
use Dits\CaW\Rest\WishlistRoute;
use Dits\CaW\Rest\WishlistShareRoute;
use Dits\CaW\Rest\WishlistTableRoute;

class RestApiConfiguration implements ConfigurationInterface {

    public function modify( Container $container ) {
        $container['rest'] = $container->service( function ( Container $container ) {
            $routes = [];

            if ( $container['settings']->get( 'enable', 'compare' ) === 'on' ) {
                $routes = array_merge( $routes, [
                    new CompareRoute( $container['compare'] ),
                    new CompareClearRoute( $container['compare'] ),
                    new CompareTableRoute( $container['compare'] ),
                    new CompareProductPostRoute( $container['compare'] ),
                    new CompareProductDeleteRoute( $container['compare'] )
                ] );
            }

            if ( $container['settings']->get( 'enable', 'wishlist' ) === 'on' ) {
                $routes = array_merge( $routes, [
                    new WishlistRoute( $container['wishlist'] ),
                    new WishlistPostRoute( $container['wishlist'] ),
                    new WishlistDeleteRoute( $container['wishlist'] ),
                    new WishlistPatchRoute( $container['wishlist'] ),
                    new WishlistShareRoute( $container['wishlist'] ),
                    new WishlistProductPostRoute( $container['wishlist'] ),
                    new WishlistProductDeleteRoute( $container['wishlist'] ),
                    new WishlistTableRoute( $container['wishlist'] )
                ] );
            }

            return new RestApi( $container['plugin_dir'], $routes );
        } );
    }
}
