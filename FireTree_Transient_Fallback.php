<?php
if ( ! class_exists( 'FireTree_Transient_Fallback' ) ) {
	
	/**
	 * Adds a fallback layer to the transient data that allows a background hook
	 * to update the transient without the end user having to wait.
	 * 
	 * @author Daniel Milner
	 * @version 1.0.0
	 */
	class FireTree_Transient_Fallback {
		
		private $prefix;
		private $fallback_expiration;
		
		/**
		 * Create a new instance
		 *
		 * @param	array	$args	An array of the arguments.
		 */
		function __construct( $args ) {
			
			$defaults = array(
				'prefix'				=> 'ft_',
				'fallback_expiration'	=> 10080,	// In minutes. Defaults to 1 week.
				'cleanup'				=> false
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$this->prefix				= $args[ 'prefix' ];
			$this->fallback_expiration	= $args[ 'fallback_expiration' ];
			
			if ( true === $args[ 'cleanup' ] ) {
			
				// Adds a hook to access the cleanup function
				add_action( 'firetree_transient_cache_cleanup', array( $this, 'cleanup' ) );
				
				// If the cleanup hook is not scheduled, then add a one-time event.
				// This is done in order to avoid having to hook into plugin activate/deactive.
				if ( false === wp_get_schedule( 'firetree_transient_cache_cleanup' ) ) {
					wp_schedule_single_event( time() + 86400, 'firetree_transient_cache_cleanup' );
				}
			
			}
			
		}
		
		/**
		 * Get the data from the transient and/or schedule new data to be retrieved.
		 *
		 * @param	string	$transient	The name of the transient. Must be 43 characters or less including $this->transient_prefix.
		 * @param	string	$hook		The name of the hook to retrieve new data.
		 * @param	array	$args		An array of arguments to pass to the function.
		 *
		 * @return	mixed	Either false or the data from the transient.
		 */
		public function get_transient( $transient, $hook, $args ) {
			
			// Build the transient names.
			$transient			= $this->prefix . $transient;
			$fallback_transient	= $transient . '_';
			
			if ( false === ( $data = get_transient( $transient ) ) ) {
				

				if ( false === ( $data = get_transient( $fallback_transient ) ) ) {
					
					// Do nothing
					
				} else {

					wp_schedule_single_event( time(), $hook, $args );
				
				}
				
				return $data;

			} else {
			
				return $data;
			
			}
			
		}
		
		/**
		 * Sets the data in both the transient and the fallback transient.
		 *
		 * @param	string	$transient	The name of the transient. Must be 43 characters or less including $this->transient_prefix.
		 * @param	mixed	$value		Transient value.
		 * @param	int		$expiration	How long you want the transient to live. In minutes.
		 *
		 * @return	boolean	TRUE/FALSE.
		 */
		public function set_transient( $transient, $value, $expiration ) {
		
			// Build the transient names.
			$transient			= $this->prefix . $transient;
			$fallback_transient	= $transient . '_';
			
			// Set the transients and store the results.
			$result = set_transient( $transient, $value, $expiration * MINUTE_IN_SECONDS );
			$fallback_result = set_transient( $fallback_transient, $value, $this->fallback_expiration * MINUTE_IN_SECONDS );
			
			if ( $result && $fallback_result ) {
			
				return true;
			
			} else {
			
				// Delete both transients in case only one was successful.
				delete_transient( $transient );
				delete_transient( $fallback_transient );
				
				return false;
				
			}
		
		}
		
		/**
		 * Purges expired transients. By using $this->prefix, we only purge transients created by this class.
		 *
		 * @return	boolean	true/false
		 */
		public function cleanup() {
		
			global $wpdb;
			
			$time_now = time();
			$expired  = $wpdb->get_col( "SELECT option_name FROM $wpdb->options where option_name LIKE '_transient_timeout_$this->prefix%' AND option_value+0 < $time_now" );
			
			if ( empty( $expired ) ) {
				return false;
			}
			
			foreach ( $expired as $transient ) {
				
				$name = str_replace( '_transient_timeout_', '', $transient );
				delete_transient( $name );
				
			}
			
			return true;
			
		}
		
	}

}
?>