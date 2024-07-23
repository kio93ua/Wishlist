<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\Admin\Fields;
use Dits\CaW\DependencyInjection\Container;

class FieldsConfiguration implements ConfigurationInterface {

	/**
	 * @inheritDoc
	 */
	public function modify( Container $container ) {
		$container['admin_fields'] = $container->service(function (Container $container) {
           return new Fields();
        });
	}
}
