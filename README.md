FireTree-Transient-Fallback
===========================

Adds a fallback layer to the transient data that allows a background hook to update the transient without the end user having to wait.

## Example of adding it to a Plugin

Add this to your main plugin file.

```php
<?php
/**
 * Setup FireTree Transient Fallback
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/FireTree_Transient_Fallback.php' );
$ft_transient_fallback_args = array(
	'prefix'				=> 'ft_',	// Prefix to add to each transient
	'fallback_expiration'	=> 10080,	// In minutes. Defaults to 1 week.
	'cleanup'				=> true		// Activate hook to purge expired transients?
);
$ft_transient_fallback = new FireTree_Transient_Fallback( $ft_transient_fallback_args );
?>
```

### Getting and Setting Transients

#### Getting a Transient

```php
<?php
$ft_transient_fallback->get_transient( $transient, $hook, $args );
?>
```

* `$transient` is the name of the transient.
* `$hook` is the name of the hook to call if the transient has expired.
* `$args` are the arguments to pass to the `$hook`.

#### Setting a Transient

```php
<?php
$ft_transient_fallback->set_transient( $transient, $value, $expiration );
?>
```

* `$transient` is the name of the transient.
* `$value` is the transient value.
* `$expiration` is how long you want the transient to live. In minutes.