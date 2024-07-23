<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistPostRoute extends AbstractRoute {

    protected Wishlist $wishlist;

    public function __construct( Wishlist $wishlist ) {
        $this->wishlist = $wishlist;
    }

    public function get_path(): string {
        return 'wishlist/list';
    }

    public function get_methods(): string {
        return 'POST';
    }

    public function get_arguments(): array {
        return [];
    }

    public function permission(): bool {
        return is_user_logged_in();
    }

    public function respond( \WP_REST_Request $request ) {
        $data = $this->wishlist->get_data();

        if ( count( $data ) >= 10 ) {
            return new FailedResponse( 'max', __( 'Maximum list created', 'dits' ) );
        }

        $new_list = [
            'id'       => uniqid(),
            'title'    => __( 'My new wishlist', 'dits' ),
            'products' => []
        ];

        array_push( $data, $new_list );

        $this->wishlist->set_data( $data );

        return new OkResponse( [ $this->wishlist->get_default_list() ], __( 'List created', 'dits' ) );
    }
}
