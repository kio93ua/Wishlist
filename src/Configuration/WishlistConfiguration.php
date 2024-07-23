<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\DependencyInjection\Container;
use Dits\CaW\Modules\Wishlist;

class WishlistConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['wishlist'] = $container->service( function ( Container $container ) {
            if ( $container['settings']->get( 'enable', 'wishlist' ) !== 'on' ) {
                return null;
            }

            return new Wishlist( $container['settings'], $container['view'], $container['plugin_dir'] );
        } );
    }
}
