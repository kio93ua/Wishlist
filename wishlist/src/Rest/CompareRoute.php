<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Compare;

class CompareRoute extends AbstractRoute {

    private Compare $compare;

    public function __construct( Compare $compare ) {
        $this->compare = $compare;
    }

    public function get_path(): string {
        return 'compare';
    }

    public function get_methods(): string {
        return 'GET';
    }

    public function get_arguments(): array {
        return [];
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ): OkResponse {
        return new OkResponse( $this->compare->get_data(), __( 'Products loaded', 'dits' ) );
    }
}
