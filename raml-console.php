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

function raml_console_enqueue_plugin_scripts($plugin_array)
{
    //enqueue TinyMCE plugin script with its ID.
    $plugin_array["raml_console_button_plugin"] =  plugin_dir_url(__FILE__) . "raml-console.js";
    return $plugin_array;
}

add_filter("mce_external_plugins", "raml_console_enqueue_plugin_scripts");

function raml_console_register_buttons_editor($buttons)
{
    //register buttons with their id.
    array_push($buttons, "raml_console");
    return $buttons;
}

add_filter("mce_buttons", "raml_console_register_buttons_editor");

//
// Include full-width template selection dropdown
//

class RAMLConsolePageTemplater {

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class.
	 */
	public static function raml_console_get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new RAMLConsolePageTemplater();
		}

		return self::$instance;

	}

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'raml_console_register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'raml_console_add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data',
			array( $this, 'raml_console_register_project_templates' )
		);


		// Add a filter to the template include to determine if the page has our
		// template assigned and return it's path
		add_filter(
			'template_include',
			array( $this, 'raml_console_view_project_template')
		);


		// Add your templates to this array.
		$this->templates = array(
			'tempate-page-fullwidth.php' => 'RAML Page Template',
		);

	}

	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function raml_console_add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function raml_console_register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list.
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		}

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function raml_console_view_project_template( $template ) {

		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta(
			$post->ID, '_wp_page_template', true
		)] ) ) {
			return $template;
		}

		$file = plugin_dir_path( __FILE__ ). get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}

}
add_action( 'plugins_loaded', array( 'RAMLConsolePageTemplater', 'raml_console_get_instance' ) );

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
