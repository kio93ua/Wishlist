<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\Modules\Compare;
use Dits\CaW\DependencyInjection\Container;

class CompareConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['compare'] = $container->service( function ( Container $container ) {

            if ( $container['settings']->get( 'enable', 'compare' ) !== 'on' ) {
                return null;
            }

            return new Compare( $container['settings'], $container['view'], $container['plugin_dir'] );
        } );

    }
}
