<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistProductDeleteRoute extends AbstractRoute {

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
                'description'       => __( 'Unique identifier for the product.' ),
                'type'              => 'integer',
                'required'          => true,
                'sanitize_callback' => function ( $param ) {
                    return absint( $param );
                },
            ]
        ];
    }

    public function get_methods(): string {
        return 'DELETE';
    }

    public function permission(): bool {
        return is_user_logged_in();
    }

    public function respond( \WP_REST_Request $request ): \WP_REST_Response {
        $product_id = $request->get_param( 'id' );
        $body       = json_decode( $request->get_body(), true );

        $current_lists = $this->wishlist->get_data();

        array_walk( $current_lists, function ( &$list ) use ( $product_id, $body ) {
            static $done = false;

            if ( $done ) {
                return;
            }

            if ( isset( $body['list_id'] ) && $body['list_id'] ) {
                if ( $list['id'] === $body['list_id'] ) {
                    $this->remove_id( $product_id, $list['products'] );
                    $done = true;
                }
            } else {
                if ( isset( $list['default'] ) && $list['default'] ) {
                    $this->remove_id( $product_id, $list['products'] );
                    $done = true;
                }
            }
        } );

        $this->wishlist->set_data( $current_lists );

        $product_title = get_the_title( $product_id );

        return new OkResponse( [ $this->wishlist->get_default_list() ], sprintf( __( 'Product %s removed from default wishlist', 'dits' ), $product_title ) );
    }

    private function remove_id( $product_id, &$array ) {
        $key = array_search( $product_id, $array );

        if ( $key !== false ) {
            array_splice( $array, $key, 1 );
        }
    }
}
