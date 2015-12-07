<?php
/*
Plugin Name: Peaceful Dragon Roles
Description: This plugin creates some custom roles and capabilities for Peaceful Dragon School.
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
Version: 0.1
License: GPL2
*/

include_once( plugin_dir_path( __FILE__ ) . '/inc/user-caps.php' );

function pd_new_roles() {
	add_role( 'basic_student', 'Basic Student', array( 'read' ) );
	add_role( 'webinar_student', 'Webinar Student', array( 'read' ) );
	add_role( 'sponsor_member', 'Sponsor Member', array( 'read' ) );
	add_role( 'pds_benefactor', 'PDS Benefactor', array( 'read' ) );
}

register_activation_hook( __FILE__, 'pd_new_roles' );

function pd_remove_roles() {
	remove_role( 'basic_student' );
	remove_role( 'webinar_student' );
	remove_role( 'sponsor_member' );
	remove_role( 'pds_benefactor' );
}

register_deactivation_hook( __FILE__, 'pd_remove_roles' );

function pd_basic( $atts, $content = null ) {
	$login_link = wp_login_url();
	$message    = "<h2>We're sorry. This content is for students only. Please <a href='" . $login_link . "'>login</a> to view.</h2>";
	if ( is_user_logged_in() ) {
		global $current_user;
		$user_role = $current_user->roles[0];
		if ( $user_role == 'basic_student' || $user_role == 'webinar_student' || $user_role == 'sponsor_member' || $user_role == 'pds_benefactor' || current_user_can( 'edit_posts' ) ) {
			return do_shortcode( $content );
		} else {
			return $message;
		}
	} else {
		return $message;
	}
}

add_shortcode( 'basic_student', 'pd_basic' );

function pd_webinar( $atts, $content = null ) {
	$login_link = wp_login_url();
	$message    = "<h2>We're sorry. This content is for webinar students only. Please <a href='" . $login_link . "'>login</a> to view.</h2>";
	if ( is_user_logged_in() ) {
		global $current_user;
		$user_role = $current_user->roles[0];
		if ( $user_role == 'webinar_student' || $user_role == 'sponsor_member' || $user_role == 'pds_benefactor' || current_user_can( 'edit_posts' ) ) {
			return do_shortcode( $content );
		} else {
			return $message;
		}
	} else {
		return $message;
	}
}

add_shortcode( 'webinar_student', 'pd_webinar' );
?>