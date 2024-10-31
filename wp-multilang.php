<?php
/*
Plugin Name: Pti's WP-Multi-language plugin
Version: 1.0
Plugin URI: http://www.ptipti.ru/wp-multilang/
Description: Different Localization for users based on cookies.
Author: Pti_the_Leader
Author URI: http://www.ptipti.ru/
*/

add_filter ('locale', 'switch_localization', 1, 1);

add_action ('admin_footer', 'draw_flags');
add_action ('wp_footer', 'draw_flags');

add_action ('admin_head', 'wp_multilang_admin_js');
add_action ('wp_head', 'wp_multilang_user_js');

function switch_localization ($locale) {
	if (is_admin ()) :
		if ($_COOKIE['admin_wp_localization']) :
			$locale = $_COOKIE['admin_wp_localization'];
			return $locale;
		endif;
	else :
		if ($_COOKIE['user_wp_localization']) :
			$locale = $_COOKIE['user_wp_localization'];
			return $locale;
		endif;
	endif;
}

function wp_multilang_admin_js () {
	echo '<script type="text/javascript">
function switch_localization_to (localization) {
	var date = new Date();
	date.setTime (date.getTime () + 7776000000);
	var expireDate = date.toGMTString ();
	document.cookie = \'admin_wp_localization=\'+localization+\'; expires=\'+expireDate+\'; path=/\';
	window.location.reload (true);
}
</script>
<style type="text/css">
	#fixedtopright {position:fixed;top:0px;left:0;right:0;border:none;height:16px;text-align:right;z-index:50;}
	#fixedtopright div {width:20px;text-align:center;float:right;background-image:url('.trailingslashit (plugins_url (basename(dirname (__FILE__)))).'abg.png);}
</style>';
}

function wp_multilang_user_js () {
	echo '<script type="text/javascript">
function switch_localization_to (localization) {
	var date = new Date();
	date.setTime (date.getTime () + (1000 * 86400 * 365));
	var expireDate = date.toGMTString ();
	document.cookie = \'user_wp_localization=\'+localization+\'; expires=\'+expireDate+\'; path=/\';
	window.location.reload (true);
}
</script>
<style type="text/css">
	#fixedtopright {position:fixed;top:0px;left:0;right:0;border:none;height:16px;text-align:right;z-index:50;}
	#fixedtopright div {width:20px;text-align:center;float:right;background-image:url('.trailingslashit (plugins_url (basename(dirname (__FILE__)))).'abg.png);}
</style>';
}

function draw_flags () {
	$localizations = get_installed_localizations ();
	echo '<div id="fixedtopright">';
	foreach ($localizations as $localization) :
		if (preg_match ('/^\w{2}\_(\w{2})$/', $localization, $matches)) :
			$country = $matches[1];
		else :
			$country = get_language_country ($localization);
		endif;
		echo '<div><a href="javascript: switch_localization_to (\''.$localization.'\');"><img src="'.trailingslashit (plugins_url (basename(dirname (__FILE__)))).'/flags/'.$country.'.png" title="'.$country.'" alt="'.$country.'" border="0" /></a></div>';
	endforeach;
	echo '</div>';
}

function get_installed_localizations () {
	if (!defined('ABSPATH')) :
		//define ('ABSPATH', dirname (__FILE__) . '/');
		echo 'UNDEFINED';
	endif;
	$localizations = array ('en');
	if ($handle = opendir (ABSPATH.'wp-content/languages')) :
		while (false !== ($file = readdir ($handle))) :
			if ($file != '.' && $file != '..' && preg_match ('/^(\w{2}\_?\w?\w?)\.mo$/', $file, $matches)) :
				$localizations[] = $matches[1];
			endif;
		endwhile;
		closedir ($handle);
	endif;
	return $localizations;
}

function get_language_country ($language) {
	switch ($language) {
		case 'ja';
			return 'JP';
		break;
		case 'uk';
			return 'UA';
		break;
		case 'en';
			return 'US';
		break;
		default;
			return 'XX';
		break;
	}
}
?>