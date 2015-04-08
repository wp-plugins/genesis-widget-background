<?php
/*
Plugin Name: Genesis Widget Background
Plugin URI: http://www.wpstud.io
Description: Adds the featured image or custom image as background of the widget.
Version: 1.2
Author: Frank Schrijvers
Author URI: http://www.wpstud.io
Text Domain: genesis-widget-background
License: GPLv2

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'WPINC' ) or die;


register_activation_hook( __FILE__, 'wpstudio_activation_check' );
/**
 * This function runs on plugin activation. It checks to make sure the required
 * minimum Genesis version is installed. If not, it deactivates itself.
 *
 *  Author: Nathan Rice
 *  Author URI: http://www.nathanrice.net/
 */
function wpstudio_activation_check() {
    $latest = '2.1.2';
    $theme_info = wp_get_theme( 'genesis' );

    if ( 'genesis' != basename( TEMPLATEPATH ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
        wp_die( sprintf( __( 'Sorry, you can\'t activate the Genesis Widget Background unless you have installed the Genesis Framework. Go back to the Plugins Page.', 'genesis-widget-background' ), '<em>', '</em>', '<a href="http://www.studiopress.com/themes/genesis" target="_blank">', '</a>', '<a href="javascript:history.back()">' ) );
    }

    if ( version_compare( $theme_info['Version'], $latest, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
        wp_die( sprintf( __( 'Sorry, you can\'t activate the Genesis Widget Background unless you have installed the Genesis Framework. Go back to the Plugins Page.', 'genesis-widget-background' ), '<em>', '</em>', '<a href="http://www.studiopress.com/themes/genesis" target="_blank">', $latest, '</a>', '<a href="javascript:history.back()">' ) );
    }
}


add_action('admin_init', 'wpstudio_deactivate_check');
/**
 * This function runs on admin_init and checks to make sure Genesis is active, if not, it 
 * deactivates the plugin. This is useful for when users switch to a non-Genesis themes.
 */
function wpstudio_deactivate_check() {
    if ( ! function_exists('genesis_pre') ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate plugin
    }
}


/**
 * Include out Widget Class file 
 */
include_once dirname( __FILE__ ) . '/inc/wpstudio-widget-class.php';


add_action( 'widgets_init', 'wpstudio_register_widget' );
/**
 * Registers our Genesis Background Widget
 */
function wpstudio_register_widget() {
     register_widget( 'Genesis_Widget_Background' );
}

add_action( 'init', 'wpstudio_load_plugin_textdomain' );
/** 
* Localization
*/
function wpstudio_load_plugin_textdomain() {
    load_plugin_textdomain('genesis-widget-background', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}