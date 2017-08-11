<?php 
class wpseo_admin_notice {
    private $_message;

    function __construct( $message ) {
        $this->_message = $message;
        add_action( 'admin_notices', array( $this, 'render' ) );
    }

    function render() {
        printf( '<div class="notice notice-success">%s</div>', $this->_message );
    }
}		