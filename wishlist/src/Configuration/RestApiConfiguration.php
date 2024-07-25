<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\DependencyInjection\Container;
use Dits\CaW\Rest\WishlistDeleteRoute;
use Dits\CaW\Rest\WishlistPatchRoute;
use Dits\CaW\Rest\WishlistPostRoute;
use Dits\CaW\Rest\WishlistProductDeleteRoute;
use Dits\CaW\Rest\WishlistProductPostRoute;
use Dits\CaW\Rest\WishlistRoute;
use Dits\CaW\Rest\WishlistShareRoute;
use Dits\CaW\Rest\WishlistTableRoute;
use Dits\CaW\Rest\RestApi; 

class RestApiConfiguration implements ConfigurationInterface {

    public function modify( Container $container ) {
        $container['rest'] = $container->service( function ( Container $container ) {
            $routes = [];

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
