<?php

namespace Dits\CaW;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class View {
    /**
     * @var Environment
     */
    private Environment $twig;

    private string $path;

    /**
     * View boot.
     */

    public function __construct( $plugin_path ) {
        $this->path = $plugin_path;
        $loader     = new FilesystemLoader( $this->path . '/templates' );
        $this->twig = new Environment( $loader, [
            'cache' => false,
        ] );
    }

    /**
     * @param string $template
     * @param array $args
     *
     * @return string
     */
    public function get( string $template, array $args = [] ): string {
        try {
            return $this->twig->render( $template, $args );
        } catch ( LoaderError | RuntimeError | SyntaxError $e ) {
            return $e;
        }
    }

    public function render( string $template, array $args = [] ) {
        echo $this->get( $template, $args );
    }
}
