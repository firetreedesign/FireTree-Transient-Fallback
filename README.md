FireTree-Transient-Fallback for WordPress
=========================================

Adds a fallback layer to the transient data that allows a background hook to update 
the transient without the end user having to wait.

## How does it work?

When `->set_transient( $transient, $value, $expiration )` is called, two transients 
are set. One that expires at the time you specified and another that expires at the 
`fallback_expiration` that was set when the class was initialized.

When `->get_transient( $transient, $hook, $args )` is called, the transient is 
checked for data. If the transient has data, then it is returned, but if the 
transient has expired, then the `$hook` is scheduled using `wp_schedule_single_event`. 
The fallback transient is then checked for data. If the fallback transient has data, 
then it is returned, but if the fallback transient has expired, then `false` is 
returned. Meanwhile, the `$hook` is scheduled to run in the background to update both 
transients with new data.

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
* `$hook` is the name of the hook to call if the transient has expired. The function that `$hook` references needs to be registered with `add_action`.
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