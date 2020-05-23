<?php

/**
 * The class that sets up admin functionality.
 *
 * This class is initiated on every page load
 * in the admin and does not have to be instantiated.
 *
 * @class       WPCampus_Jobs_Admin
 * @package     WPCampus_Jobs
 */
final class WPCampus_Jobs_Admin {

	/**
	 * We don't need to instantiate this class.
	 */
	protected function __construct() { }

	/**
	 * Registers all of our hooks and what not.
	 */
	public static function register() {
		$plugin = new self();

		add_action( 'admin_menu', [ $plugin, 'add_settings_page' ] );

		add_action( 'admin_menu', [ $plugin, 'remove_meta_boxes' ], 100 );

	}

	/**
	 * Add the jobs settings page.
	 */
	public function add_settings_page() {

		$post_type = 'job';
		$page_title = 'WPCampus Job Board settings';
		$menu_title = 'Settings';
		$menu_slug = 'wpcampus-jobs-settings';
		$capability = 'manage_options'; // @TODO customize with jobs capabilities

		// Add default WordPress page if ACF doesn't exist.
		if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
			add_submenu_page(
				'edit.php?post_type=' . $post_type,
				$page_title,
				$menu_title,
				$capability,
				$menu_slug,
				[ $this, 'print_default_settings_page' ]
			);
			return;
		}

		acf_add_options_sub_page(
			[
				'page_title'  => $page_title,
				'menu_title'  => $menu_title,
				'capability'  => $capability,
				'menu_slug'   => $menu_slug,
				'parent_slug' => 'edit.php?post_type=' . $post_type,
			]
		);

		$this->add_acf_fields();
	}

	/**
	 * Print default options page which tells the user
	 * to enable ACF in order to access settings.
	 */
	public function print_default_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p>The Exec Ed settings require the Advanced Custom Fields PRO plugin. <a href="<?php echo admin_url( 'plugins.php' ); ?>">Manage plugins</a></p>
		</div>
		<?php
	}

	/**
	 * Register our ACF fields.
	 */
	private function add_acf_fields() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'inc/wpcampus-jobs-fields.php';
	}

	/**
	 * Removes meta boxes we don't need.
	 */
	public function remove_meta_boxes() {

		foreach ( [ 'job_category', 'job_type' ] as $taxonomy ) {
			remove_meta_box( "tagsdiv-{$taxonomy}", 'job', 'side' );
		}
	}
}

WPCampus_Jobs_Admin::register();