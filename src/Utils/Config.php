<?php

namespace Dits\CaW\Utils;

// Exit if accessed directly

defined( 'WPINC' ) || die;

class Config {
    private string $file;

    /**
     * All config data
     *
     * @var array
     */
    protected array $data = [];

    public function __construct( string $file ) {
        $this->file = $file;

        $this->load_data();
    }

    /**
     * Load files config data
     */
    private function load_data() {
        $ext = '.php';
        $dir = realpath( dirname( $this->file ) ) . '/config/';
        $pattern = sprintf( '%s*%s', $dir, $ext );

        foreach ( glob( $pattern ) as $filepath ) {
            $key = str_replace( $dir, '', $filepath );
            $key = str_replace( $ext, '', $key );

            if ( is_file( $filepath ) ) {
                $this->data[ $key ] = include $filepath;
            }
        }
    }

    /**
     * Get data value
     *
     * @param string $config
     * @param string $key
     *
     * @return array|mixed
     */
    public function get( string $config, string $key = '' ) {
        $data = $this->data[ $config ] ?? [];

        if ( $key !== '' ) {
            if ( strpos( $key, '.' ) === false ) {
                return $data[ $key ] ?? $data;
            }

            foreach ( explode( '.', $key ) as $segment ) {
                if ( is_array( $data ) && array_key_exists( $segment, $data ) ) {
                    $data = $data[ $segment ];
                }
            }
        }

        return $data;
    }

    /**
     * Set data value
     *
     * @param string $config
     * @param string $key
     * @param $value
     *
     * @return mixed|false
     */
    public function set( string $config, string $key, $value ) {
        $array = &$this->data[ $config ];

        $keys = explode( '.', $key );

        while ( count( $keys ) > 1 ) {
            $key = array_shift( $keys );

            if ( ! isset( $array[ $key ] ) || ! is_array( $array[ $key ] ) ) {
                $array[ $key ] = [];
            }

            $array = &$array[ $key ];
        }

        $array[ array_shift( $keys ) ] = $value;

        return $array;
    }
}
