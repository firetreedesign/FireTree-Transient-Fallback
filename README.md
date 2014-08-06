FireTree-Transient-Cache
========================

Adds a fallback layer to the transient data that allows a background hook to update the transient without the end user having to wait.

## Example of adding it to a Plugin

```php
/**
 * Setup FireTree Transient Caching
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/FireTree_Transient_Cache.php' );
$ft_transient_cache_args = array(
	'prefix'	=> 'ft_',
	'cleanup'	=> true
);
$ft_transient_cache = new FireTree_Transient_Cache( $ft_transient_cache_args );
```