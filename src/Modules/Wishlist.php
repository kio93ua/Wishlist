<?php

namespace Dits\CaW\Modules;

class Wishlist extends AbstractModule {
    protected string $meta_key = 'dcaw_wished_products';

    public function get_module_name(): string {
        return 'wishlist';
    }

    public function get_config(): array {
        return [
            'selectors'   => [
                'counters'       => '.js-dcaw-wishlist-count',
                'button'         => '.js-dcaw-wishlist-btn',
                'remove'         => '.js-dcaw-wishlist-remove-btn',
                'tables'         => '.dcaw-wishlist',
                'createWishlist' => '.js-create-wishlist'
            ],
            'classes'     => [
                'counter' => 'dcaw-wishlist-count',
                'empty'   => 'is-empty',
                'active'  => 'is-active',
                'loading' => 'is-loading'
            ],
            'accountLink' => get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ),
        ];
    }

    public function hooks() {
        parent::hooks();

        add_action( 'init', [ $this, 'wishlist_endpoint' ] );
        add_action( 'init', [ $this, 'register_rewrite_rule' ] );
        add_filter( 'woocommerce_account_menu_items', [ $this, 'menu_links' ], 40 );
        add_action( 'woocommerce_account_wishlist_endpoint', [ $this, 'wishlist_endpoint_content' ] );
        add_action( 'template_include', [ $this, 'redirect_shared_wishlist' ] );
    }

    // FIXME
    public function redirect_shared_wishlist( $original_template ) {
        if ( ! get_query_var( 'user_id' ) && ! get_query_var( 'wishlist_id' ) ) {
            return $original_template;
        }

        $user_id     = get_query_var( 'user_id' );
        $wishlist_id = get_query_var( 'wishlist_id' );
        $data        = get_user_meta( $user_id, $this->meta_key, true );

        $key = array_search( $wishlist_id, array_combine(
            array_keys( $data ),
            array_column( $data, 'id' )
        ) );

        if ( empty( $data ) || $key === false ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );

            return get_template_part( 404 );
        }

        $args['title'] = $data[ $key ]['title'];

        if ( empty( $data[ $key ]['products'] ) ) {
            $args['empty'] = $this->view->get( 'empty-content.twig', [
                'svg'         => $this->view->get( 'icons/empty.svg' ),
                'title'       => __( 'This wishlist is empty', 'dits' ),
                'button_text' => __( 'Go back', 'dits' ),
                'button_link' => $_SERVER['HTTP_REFERER'] ?? home_url()
            ] );
        } else {
            $args['products'] = array_map( function ( $product_id ) {
                $_product = \wc_get_product( $product_id );

                return [
                    'img'        => $_product->get_image(),
                    'title'      => $_product->get_name(),
                    'link'       => $_product->get_permalink(),
                    'price'      => $_product->get_price_html(),
                    'buy'        => do_shortcode( '[add_to_cart id="' . $product_id . '" show_price="false" style=""]' ),
                    'compare'    => $this->button( [ 'id' => $product_id ] ),
                    'attributes' => []
                ];
            }, $data[ $key ]['products'] );
        }

        ob_start();
        get_header();
        $args['header'] = ob_get_clean();

        ob_start();
        get_footer();
        $args['footer'] = ob_get_clean();

        $this->view->render( 'shared-wishlist.twig', $args );
    }

    public function register_rewrite_rule() {
        add_rewrite_rule(
            '^shared-wishlist/([^/]*)/([^/]*)/?',
            'index.php?user_id=$matches[1]&wishlist_id=$matches[2]',
            'top'
        );
        add_rewrite_tag( '%wishlist_id%', '([^&]+)' );
        add_rewrite_tag( '%user_id%', '([^&]+)' );
    }

    public function wishlist_endpoint() {
        add_rewrite_endpoint( 'wishlist', EP_PAGES );
    }

    public function menu_links( $menu_links ): array {
        return array_slice( $menu_links, 0, 4, true )
               + [ 'wishlist' => __( 'Wishlist', 'dits' ) ]
               + array_slice( $menu_links, 4, null, true );
    }

    public function wishlist_endpoint_content() {
        $args = [
            'title'       => __( 'Wishlist', 'dits' ),
            'action_text' => __( 'Add new wishlist', 'dits' ),
            'action_icon' => $this->view->get( 'icons/plus.svg' ),
            'content'     => $this->table()
        ];

        $this->view->render( 'wishlist.twig', $args );
    }

    public function button( $atts = [] ): string {
        $args = $this->get_button_args( $atts['id'] ?? null );

        $product_id   = $args['id'];
        $default_list = $this->get_default_list();

        if ( in_array( $product_id, $default_list['products'] ) ) {
            $args['class'] .= ' is-active';
        }

        return $this->view->get( 'wishlist-button.twig', $args );
    }

    public function get_default_list() {
        foreach ( $this->get_data() as $list ) {
            if ( isset( $list['default'] ) && $list['default'] === true ) {
                return $list;
            }
        }

        return [];
    }

    public function get_default_data(): array {
        return [
            [
                'id'       => uniqid(),
                'title'    => __( 'My wishlist', 'dits' ),
                'default'  => true,
                'products' => []
            ]
        ];
    }

    public function load_data(): void {
        $data = $this->get_default_data();

        if ( $user_id = get_current_user_id() ) {
            $user_meta = get_user_meta( $user_id, $this->meta_key, true );
            $data      = $user_meta ?: $data;
        }

        $this->set_data( $data );
    }

    public function set_data( array $data ) {
        if ( $user_id = get_current_user_id() ) {
            update_user_meta( $user_id, $this->meta_key, $data );
        }

        $this->data = $data;
    }

    public function table(): string {
        $html = '';

        foreach ( $this->get_data() as $list ) {
            $products = [];
            $list_id  = $list['id'];

            if ( empty( $list['products'] ) ) {
                $args['empty_content'] = $this->view->get( 'empty-content.twig', [
                    'svg'         => $this->view->get( 'icons/empty.svg' ),
                    'title'       => __( 'Your wishlist is empty', 'dits' ),
                    'text'        => __( 'Fill it with goods', 'dits' ),
                    'button_text' => __( 'Go to shop', 'dits' ),
                    'button_link' => \get_permalink( \get_option( 'woocommerce_shop_page_id' ) )
                ] );
            } else {
                foreach ( $list['products'] as $product_id ) {
                    $_product = wc_get_product( $product_id );

                    // Get product info
                    $products[ $product_id ] = [
                        'img'   => $_product->get_image(),
                        'title' => $_product->get_name(),
                        'link'  => $_product->get_permalink(),
                        'price' => $_product->get_price_html(),
                        'buy'   => do_shortcode( '[add_to_cart id="' . $product_id . '" show_price="false" style=""]' ),
                    ];
                }
            }

            $args['products']  = $products;
            $args['title']     = $list['title'];
            $args['id']        = $list_id;
            $args['count']     = count( $products );
            $args['move_form'] = [
                'text'  => __( 'Move', 'dits' ),
                'icon'  => $this->view->get( 'icons/switch.svg' ),
                'lists' => array_filter( $this->get_data(), function ( $l ) use ( $list_id ) {
                    return $l['id'] !== $list_id;
                } )
            ];
            $args['edit_form'] = [
                'text' => __( 'Send', 'dits' ),
                'icon' => $this->view->get( 'icons/check.svg' )
            ];
            $args['edit']      = [
                'text' => __( 'Edit', 'dits' ),
                'icon' => $this->view->get( 'icons/edit.svg' )
            ];
            $args['actions']   = [
                'default' => [
                    'text' => __( 'Make default', 'dits' ),
                    'icon' => $this->view->get( 'icons/star.svg' )
                ],
                'share'   => [
                    'text' => __( 'Share', 'dits' ),
                    'icon' => $this->view->get( 'icons/share.svg' )
                ],
                'delete'  => [
                    'text' => __( 'Delete', 'dits' ),
                    'icon' => $this->view->get( 'icons/trash.svg' )
                ],
            ];
            $args['default']   = [
                'active' => $list['default'] ?? false,
                'text'   => __( '(default)', 'dits' )
            ];

            $html .= $this->view->get( 'wishlist-table.twig', $args );
        }

        return $html;
    }
}
