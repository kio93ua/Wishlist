<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistProductPostRoute extends AbstractRoute {

    private Wishlist $wishlist;

    public function __construct( Wishlist $wishlist ) {
        $this->wishlist = $wishlist;
    }

    public function get_path(): string {
        return 'wishlist/product/(?P<id>\d+)';
    }

    public function get_arguments(): array {
        return [
            'id' => [
                'description' => __( 'Unique identifier for the product.' ),
                'type'        => 'integer',
                'required'    => true,
                'sanitize_callback' => function ( $param ) {
                    return absint( $param );
                },
            ]
        ];
    }

    public function get_methods(): string {
        return 'POST';
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ): \WP_REST_Response {
        if ( ! is_user_logged_in() ) {
            return new FailedResponse('guest', __('Need login', 'dits') );
        }

        $product_id = absint( $request->get_param( 'id' ) );

        $current_lists = $this->wishlist->get_data();

        array_walk( $current_lists, function ( &$list ) use ( $product_id ) {
            if ( isset( $list['default'] ) && $list['default'] ) {
                array_push( $list['products'], $product_id );
            }
        } );

        $this->wishlist->set_data( $current_lists );

        $product_title = get_the_title( $product_id );

        return new OkResponse( [ $this->wishlist->get_default_list() ], sprintf( __( 'Product %s added to default wishlist', 'dits' ), $product_title ) );
    }
}
