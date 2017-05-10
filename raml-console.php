<?php
/*
Plugin Name: RAML Console Plugin
Plugin URI: http://www.mulesoft.com
Description: This plugin allows you to embedd a RAML Console in your post or page.
Author: Dejim Juang
Version: 1.0
Author URI: http://dejim.com
*/

// ----------------------------------------------------------
// raml-console shortcode

function raml_console_shortcode($atts = [], $content = null, $tag = ''){

  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // override default attributes with user attributes
  $wporg_atts = shortcode_atts([
                                   'file' => 'https://anypoint.mulesoft.com/apiplatform/repository/v2/organizations/b85b361d-0842-4b70-af37-2723c2c3ae7e/public/apis/59911/versions/62316/files/root',
                               ], $atts, $tag);

?>
<link href="<?php echo plugin_dir_url( __FILE__ ) . 'styles/api-console-light-theme.css'; ?>" rel="stylesheet" class="theme">

<body ng-app="ramlConsoleApp" ng-cloak class="raml-console-body">
  <script src="<?php echo plugin_dir_url( __FILE__ ) . 'scripts/api-console-vendor.js'; ?>"></script>
  <script src="<?php echo plugin_dir_url( __FILE__ ) . 'scripts/api-console.js'; ?>"></script>
  <script>
    $.noConflict();
  </script>

  <div style="overflow:auto; position:relative">
    <raml-console-loader options="{ disableThemeSwitcher: true, disableRamlClientGenerator: true, disableTitle: true }"
      src="<?php echo esc_html__($wporg_atts['file'], 'wporg'); ?>"></raml-console>
  </div>
</body>

<?php
}

function raml_console_shortcodes_init()
{
    add_shortcode('raml-console', 'raml_console_shortcode');
}

add_action('init', 'raml_console_shortcodes_init');

// ----------------------------------------------------------
// Add MCE plugin below

function enqueue_plugin_scripts($plugin_array)
{
    //enqueue TinyMCE plugin script with its ID.
    $plugin_array["raml_console_button_plugin"] =  plugin_dir_url(__FILE__) . "raml-console.js";
    return $plugin_array;
}

add_filter("mce_external_plugins", "enqueue_plugin_scripts");

function register_buttons_editor($buttons)
{
    //register buttons with their id.
    array_push($buttons, "raml_console");
    return $buttons;
}

add_filter("mce_buttons", "register_buttons_editor");


// ----------------------------------------------------------
// raml-console Admin Menu
// TODO - Add settings for the plugin

/*
add_action( 'admin_menu', 'raml_console_add_admin_menu' );
add_action( 'admin_init', 'raml_console_settings_init' );


function raml_console_add_admin_menu(  ) {

	add_options_page( 'RAML Console', 'RAML Console', 'manage_options', 'raml_console', 'raml_console_options_page' );

}


function raml_console_settings_init(  ) {

	register_setting( 'pluginPage', 'raml_console_settings' );

	add_settings_section(
		'raml_console_pluginPage_section',
		__( 'Your section description', 'wordpress' ),
		'raml_console_settings_section_callback',
		'pluginPage'
	);

	add_settings_field(
		'raml_console_text_field_0',
		__( 'Settings field description', 'wordpress' ),
		'raml_console_text_field_0_render',
		'pluginPage',
		'raml_console_pluginPage_section'
	);


}


function raml_console_text_field_0_render(  ) {

	$options = get_option( 'raml_console_settings' );
	?>
	<input type='text' name='raml_console_settings[raml_console_text_field_0]' value='<?php echo $options['raml_console_text_field_0']; ?>'>
	<?php

}


function raml_console_settings_section_callback(  ) {

	echo __( 'This section description', 'wordpress' );

}


function raml_console_options_page(  ) {

	?>
	<form action='options.php' method='post'>

		<h2>RAML Console</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}
*/


?>
