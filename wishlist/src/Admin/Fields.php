<?php

namespace Dits\CaW\Admin;

class Fields {
    public function __construct() {
        // TODO
    }

    public function get_defaults_field( $str ): array {
        return [
            [
                'id'      => 'enable',
                'label'   => sprintf( __( 'Enable %s', 'dits' ), $str ),
                'desc'    => sprintf( __( 'Turn on %s functionality', 'dits' ), $str ),
                'type'    => 'checkbox',
                'default' => '',
            ],
            [
                'id'      => 'single_button_hook',
                'label'   => __( 'Single product button hook', 'dits' ),
                'desc'    => sprintf( __( 'Hook for display %s button.', 'dits' ), $str ),
                'type'    => 'select',
                'options' => [
                    'woocommerce_before_add_to_cart_form'   => 'woocommerce_before_add_to_cart_form',
                    'woocommerce_before_add_to_cart_button' => 'woocommerce_before_add_to_cart_button',
                    'woocommerce_after_add_to_cart_button'  => 'woocommerce_after_add_to_cart_button',
                    'woocommerce_after_add_to_cart_form'    => 'woocommerce_after_add_to_cart_form',
                    'manual'                                => __( 'Manual', 'dits' ) . ' (use shortcode [dcaw_' . $str . '_button])'
                ],
                'default' => 'manual'
            ],
            [
                'id'      => 'loop_button_hook',
                'label'   => __( 'In loop button hook', 'dits' ),
                'desc'    => sprintf( __( 'Hook for display %s button.', 'dits' ), $str ),
                'type'    => 'select',
                'options' => [
                    'before' => 'Before add to cart button',
                    'after'  => 'After add to cart button',
                    'manual' => __( 'Manual', 'dits' ) . ' (use shortcode [dcaw_' . $str . '_button])'
                ],
                'default' => 'manual'
            ],
            [
                'id'    => 'button_class',
                'label' => __( 'Button class', 'dits' ),
                'desc'  => sprintf( __( 'Add to %s button css classes (separate comma)', 'dits' ), $str ),
                'type'  => 'text',
            ],
            [
                'id'      => 'button_text',
                'label'   => __( 'Button text', 'dits' ),
                'desc'    => sprintf( __( '%s button text', 'dits' ), $str ),
                'type'    => 'text',
                'default' => sprintf( __( '%s', 'dits' ), ucfirst( $str ) ),
            ],
            [
                'id'    => 'button_icon',
                'label' => __( 'Button svg icon', 'dits' ),
                'desc'  => __( 'Svg icon code', 'dits' ),
                'type'  => 'textarea',
            ],
            [
                'id'      => 'count_selector',
                'label'   => sprintf( __( 'Css selector for %s counters', 'dits' ), $str ),
                'type'    => 'text',
                'desc'    => sprintf( __( 'Default selector .js-dcaw-%s-count', 'dits' ), $str ),
                'default' => ''
            ],
        ];
    }

    public function get_compare_fields(): array {
        return [
            [
                'id'      => 'max',
                'label'   => __( 'Max compared', 'dits' ),
                'desc'    => __( 'Max product to compare', 'dits' ),
                'type'    => 'number',
                'default' => 6
            ],
            [
                'id'      => 'attributes',
                'label'   => __( 'Compare attributes', 'dits' ),
                'type'    => 'checkbox',
                'default' => '',
            ],
            [
                'id'      => 'taxonomies',
                'label'   => __( 'Compare taxonomies', 'dits' ),
                'type'    => 'multicheck',
                'options' => $this->get_compare_taxonomies()
            ],
            [
                'id'      => 'dimensions',
                'label'   => __( 'Compare dimensions', 'dits' ),
                'type'    => 'multicheck',
                'options' => [
                    'length' => __( 'Length', 'dits' ),
                    'height' => __( 'Height', 'dits' ),
                    'width'  => __( 'Width', 'dits' ),
                    'weight' => __( 'Weight', 'dits' ),
                ]
            ]
        ];
    }

    public function get_wishlist_compare(): array {
        return [];
    }

    public function get_sections(): array {
        return [
            [
                'id'       => null,
                'title'    => __( 'admin_page.welcome_title', 'dits' ),
                'desc'     => null,
                'callback' => null
            ],
            [
                'id'    => 'compare',
                'title' => __( 'admin_page.compare_title', 'dits' )
            ],
            [
                'id'    => 'wishlist',
                'title' => __( 'admin_page.wishlist_title', 'dits' )
            ]
        ];
    }

    public function get_fields(): array {
        return [
            'compare'  => array_merge(
                $this->get_defaults_field( 'compare' ),
                $this->get_compare_fields()
            ),
            'wishlist' => array_merge(
                $this->get_defaults_field( 'wishlist' ),
                $this->get_wishlist_compare()
            )
        ];
    }

    private function get_compare_taxonomies(): array {
        $taxonomies = get_object_taxonomies( 'product', 'objects' );

        $taxonomies = array_filter( $taxonomies, function ( $item ) {
            return $item->show_ui;
        } );

        return array_map( function ( $taxonomy ) {
            return $taxonomy->label;
        }, $taxonomies );
    }
}
