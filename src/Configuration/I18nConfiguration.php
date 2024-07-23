<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\DependencyInjection\Container;
use Dits\CaW\I18n;

class I18nConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['i18n'] = $container->service( function ( Container $container ) {
            return new I18n( $container['plugin_domain'], $container['plugin_dir'] );
        } );
    }
}
