<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Compare;

class CompareClearRoute extends AbstractRoute {

    private Compare $compare;

    public function __construct( Compare $compare ) {
        $this->compare = $compare;
    }

    public function get_path(): string {
        return 'compare';
    }

    public function get_methods(): string {
        return 'DELETE';
    }

    public function get_arguments(): array {
        return [];
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ): \WP_REST_Response {
        // FIXME check
        $this->compare->set_data( $this->compare->get_default_data() );

        return new OkResponse( $this->compare->get_data(), __( 'Compare cleared', 'dits' ) );
    }
}
