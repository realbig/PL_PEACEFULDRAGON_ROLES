<?php

/**
 * Class FilterUsersCaps
 *
 * Allows filtering of specific user capabilities.
 *
 * @package WordPress
 * @subpackage FilterUserCaps
 *
 * @version 0.1
 */
class FilterUsersCaps {

	/**
	 * Array of users and capabilities to allow for each user.
	 *
	 * Supply a wildcard (*) for the ID and the caps will apply to all users.
	 *
	 * @since FilterUserCaps 0.1
	 */
	public $allowed_caps = array(
		'*' => array(
			'read',
		),
		104 => array(

			// Edit Users
			'edit_user',
			'manage_network_users',
		),
	);

	/**
	 * Array of users and capabilities to disallow for each user.
	 *
	 * Supply a wildcard (*) for the ID and the caps will apply to all users.
	 *
	 * @since FilterUserCaps 0.1
	 */
	public $disallowed_caps = array(
		104 => array(

			// Plugins
			'activate_plugins',
			'delete_plugins',
			'update_plugins',
			'install_plugins',
			'edit_plugins',
			// Themes
			'install_themes',
			'delete_themes',
			'edit_themes',
			'switch_themes',
			'update_themes',
		),
	);

	/**
	 * The class construct function. Adds the filter "user_has_cap".
	 *
	 * The filter "user_has_cap" is used in the WP Core function "has_cap". This function is used
	 * throughout WP Core whenever checking if the current user has a specific capabilitiy. So by
	 * hooking into this filter, we can trick WP into thinking any user has any cap (or doesn't have
	 * any cap).
	 *
	 * @since FilterUserCaps 0.1
	 */
	function __construct() {

		// Add the filter
		add_filter( 'user_has_cap', array( $this, 'filter_user_caps' ), 10, 4 );
	}

	/**
	 * The function that hooks into the filter "user_has_cap".
	 *
	 * @since FilterUserCaps 0.1
	 *
	 * @param array $caps The current user's capabilities.
	 * @param array $null (Not being used) Possibly holds "do_not_allow".
	 * @param array $args The current capability to check against.
	 * @param object $user The current user object.
	 *
	 * @return array The filtered (or possibly unfiltered) capabilities.
	 */
	public function filter_user_caps( $caps, $null, $args, $user ) {

		// If the current user does not have any filtering, return the capabilities unfiltered
		// OR if there is a wildcard in the array
		if ( ! array_key_exists( $user->ID, $this->allowed_caps )
		     && ! array_key_exists( $user->ID, $this->disallowed_caps )
		     && ! array_key_exists( '*', $this->allowed_caps )
		     && ! array_key_exists( '*', $this->disallowed_caps )
		) {
			return $caps;
		}

		// If the argument we're filtering for is in our array, then add it
		// Also add the "do_not_allow" cap to override some multi-site functionality
		if ( in_array( $args[0], $this->allowed_caps[ $user->ID ] ) ) {
			$caps['do_not_allow'] = true;

			$caps[ $args[0] ] = true;
		}

		// If the argument we're filtering for is in our array, then remove it
		if ( in_array( $args[0], $this->disallowed_caps[ $user->ID ] ) ) {
			unset( $caps[ $args[0] ] );
		}

		// Wildcards
		// Do it all again, but for wildcard selectors
		if ( array_key_exists( '*', $this->allowed_caps ) && in_array( $args[0], $this->allowed_caps['*'] ) ) {
			$caps['do_not_allow'] = true;

			$caps[ $args[0] ] = true;
		}

		if ( array_key_exists( '*', $this->allowed_caps ) && in_array( $args[0], $this->disallowed_caps['*'] ) ) {
			unset( $caps[ $args[0] ] );
		}

		// Return our filtered caps
		return $caps;
	}
}

new FilterUsersCaps();