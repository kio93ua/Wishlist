<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistPatchRoute extends AbstractRoute {

    protected Wishlist $wishlist;

    public function __construct( Wishlist $wishlist ) {
        $this->wishlist = $wishlist;
    }

    public function get_path(): string {
        return 'wishlist/list/(?P<id>[a-zA-Z0-9]+)';
    }

    public function get_methods(): string {
        return 'PATCH';
    }

    public function get_arguments(): array {
        return [
            'id' => [
                'description'       => __( 'Unique identifier for the list.' ),
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => function ( $param ) {
                    return htmlspecialchars( $param );
                },
            ]
        ];
    }

    public function permission(): bool {
        return is_user_logged_in();
    }

    public function respond( \WP_REST_Request $request ): OkResponse {
        $list_id  = $request->get_param( 'id' );
        $body     = json_decode( $request->get_body(), true );
        $data     = $this->wishlist->get_data();
        $new_list = [];

        if ( isset( $body['prev_list'], $body['next_list'] ) && $body['prev_list'] && $body['next_list'] ) {
            $prev_list = htmlspecialchars( $body['prev_list'] );
            $next_list = htmlspecialchars( $body['next_list'] );
            $products  = array_map( 'absint', $body['products'] );

            array_walk( $data, function ( &$list ) use ( $prev_list, $products, $next_list ) {
                if ( $prev_list === $list['id'] ) {
                    // remove products
                    $list['products'] = array_values( array_diff( $list['products'], $products ) );
                }

                if ( $next_list === $list['id'] ) {
                    $list['products'] = array_values( array_unique( array_merge( $list['products'], $products ) ) );
                }

            } );
        }

        if ( isset( $body['default'] ) && $body['default'] === true ) {
            $new_list['default'] = true;

            array_walk( $data, function ( &$list ) {
                unset( $list['default'] );
            } );
        }

        if ( isset( $body['title'] ) && $body['title'] ) {
            $new_list['title'] = htmlspecialchars( $body['title'] );
        }

        if ( ! empty( $new_list ) ) {
            array_walk( $data, function ( &$list ) use ( $list_id, $new_list ) {
                if ( $list['id'] === $list_id ) {
                    $list = array_merge( $list, $new_list );
                }
            } );
        }

        $this->wishlist->set_data( $data );

        return new OkResponse( [ $this->wishlist->get_default_list() ], __( 'List changed', 'dits' ) );
    }
}
