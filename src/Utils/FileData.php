<?php

namespace Dits\CaW\Utils;

// Exit if accessed directly
defined( 'WPINC' ) || die;

class FileData {
    private string $filename;
    private string $path;

    public function __construct( string $filename, string $path = '' ) {
        $this->filename = $filename;
        $this->path     = realpath( $path );
    }

    /**
     * @return string
     */

    public function path(): string {
        return realpath( join( DIRECTORY_SEPARATOR, [ $this->path, $this->filename ] ) );
    }

    public function url(): string {
        $plugins_url     = plugins_url();
        $plugin_basename = plugin_basename( $this->path );

        return join( '/', [ $plugins_url, $plugin_basename, $this->filename ] );
    }
}
