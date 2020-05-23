<?php

/**
 * The class that sets up
 * global plugin functionality.
 *
 * This class is initiated on every page
 * load and does not have to be instantiated.
 *
 * @class       WPCampus_Jobs_Global
 * @package     WPCampus_Jobs
 */
final class WPCampus_Jobs_Global {

	/**
	 * We don't need to instantiate this class.
	 */
	protected function __construct() { }

	/**
	 * Registers all of our hooks and what not.
	 */
	public static function register() {
		$plugin = new self();

		register_activation_hook( __FILE__, [ $plugin, 'activate' ] );

		add_action( 'init', [ $plugin, 'register_taxonomies' ], 0 );
		add_action( 'init', [ $plugin, 'register_custom_post_type' ], 1 );

		add_filter( 'acf/load_field/name=wpc_submit_job_form', [ $plugin, 'load_form_field_choices' ] );
		add_filter( 'acf/load_field/name=wpc_edit_job_form', [ $plugin, 'load_form_field_choices' ] );

		// Setup form filters.
		$submit_form_id = get_option( 'options_wpc_submit_job_form' );
		$edit_form_id = get_option( 'options_wpc_submit_job_form' );

		// Filter submit job form.
		if ( $submit_form_id > 0 ) {
			add_filter( 'gform_pre_render_' . $submit_form_id, [ $plugin, 'filter_jobs_form' ] );
		}

		// Filter edit job form.
		if ( $edit_form_id > 0 && $edit_form_id !== $submit_form_id ) {
			add_filter( 'gform_pre_render_' . $edit_form_id, [ $plugin, 'filter_jobs_form' ] );
		}
	}

	/**
	 * Runs when plugin is activated.
	 */
	function activate() {

		register_taxonomies();
		register_custom_post_type();

		flush_rewrite_rules();

	}

	/**
	 * Registers job taxonomies.
	 */
	function register_taxonomies() {

		register_taxonomy(
			'job_category',
			[ 'job' ],
			[
				'labels'            => [
					'name'                       => 'Job categories',
					'singular_name'              => 'Job category',
					'menu_name'                  => 'Job categories',
					'all_items'                  => 'All job categories',
					'new_item_name'              => 'New job category',
					'add_new_item'               => 'Add new job category',
					'edit_item'                  => 'Edit job category',
					'update_item'                => 'Update job category',
					'view_item'                  => 'View job category',
					'separate_items_with_commas' => 'Separate job categories with commas',
					'add_or_remove_items'        => 'Add or remove job categories',
					'choose_from_most_used'      => 'Choose from the most used job categories',
					'popular_items'              => 'Popular job categories',
					'search_items'               => 'Search job categories',
					'no_terms'                   => 'No job categories',
					'items_list'                 => 'Job categories list',
					'items_list_navigation'      => 'Job categories list navigation',
				],
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_tagcloud'     => true,
			]
		);

		register_taxonomy(
			'job_type',
			[ 'job' ],
			[
				'labels'            => [
					'name'                       => 'Job types',
					'singular_name'              => 'Job type',
					'menu_name'                  => 'Job types',
					'all_items'                  => 'All job types',
					'new_item_name'              => 'New job type',
					'add_new_item'               => 'Add new job type',
					'edit_item'                  => 'Edit job type',
					'update_item'                => 'Update job type',
					'view_item'                  => 'View job type',
					'separate_items_with_commas' => 'Separate job types with commas',
					'add_or_remove_items'        => 'Add or remove job types',
					'choose_from_most_used'      => 'Choose from the most used job types',
					'popular_items'              => 'Popular job types',
					'search_items'               => 'Search job types',
					'no_terms'                   => 'No job types',
					'items_list'                 => 'Job types list',
					'items_list_navigation'      => 'Job types list navigation',
				],
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_tagcloud'     => true,
			]
		);
	}

	/**
	 * Registers the jobs custom post type.
	 */
	public function register_custom_post_type() {

		register_post_type(
			'job',
			[
				'label'               => 'Jobs',
				'labels'              => [
					'name'           => 'Jobs',
					'singular_name'  => 'Job',
					'menu_name'      => 'Jobs',
					'name_admin_bar' => 'Jobs',
					'archives'       => 'Job Archives',
					'attributes'     => 'Job Attributes',
					'all_items'      => 'All Jobs',
					'add_new_item'   => 'Add New Job',
					'new_item'       => 'New Job',
					'edit_item'      => 'Edit Job',
					'update_item'    => 'Update Job',
					'view_item'      => 'View Job',
					'view_items'     => 'View Jobs',
					'search_items'   => 'Search Jobs',
				],
				'supports'            => [ 'title', 'editor', 'revisions', 'author', 'thumbnail' ],
				'taxonomies'          => [ 'job_type' ],
				'hierarchical'        => false,
				'public'              => true,
				'menu_icon'           => 'dashicons-clipboard',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'capability_type'     => [ 'job', 'jobs' ],
				'show_in_rest'        => true,
				'rewrite'             => [
					'slug'       => 'jobs',
					'with_front' => false,
				],
			]
		);
	}

	/**
	 * Modifies ACF field to include list of Gravity Form choices.
	 *
	 * @param $field - array
	 *
	 * @return array
	 */
	public function load_form_field_choices( $field ) {

		// Reset choices.
		$field['choices'] = [];

		// Get list of forms.
		$forms = class_exists( 'GFAPI' ) ? GFAPI::get_forms() : [];

		if ( empty( $forms ) ) {
			return $field;
		}

		foreach ( $forms as $form ) {

			$form_id = $form['id'];
			if ( empty( $form_id ) ) {
				continue;
			}

			$form_title = $form['title'];
			if ( empty( $form_title ) ) {
				continue;
			}

			$field['choices'][ $form_id ] = $form_title;

		}

		return $field;
	}

	/**
	 * Build list of form choices from taxonomy terms.
	 *
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	private function get_tax_term_choices( string $taxonomy ): array {

		$types = $terms = get_terms(
			$taxonomy,
			[
				'hide_empty' => false,
			]
		);

		if ( empty( $types ) ) {
			return [];
		}

		$choices = [];

		foreach ( $types as $type ) {
			$choices[] = [
				'text'  => $type->name,
				'value' => (int) $type->term_id,
			];
		}

		return $choices;
	}

	/**
	 * Filter the edit job posting form.
	 *
	 * @param $form - array - the form object
	 *
	 * @return array
	 */
	public function filter_jobs_form( $form ) {

		if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			return $form;
		}

		$fields = $form['fields'];

		foreach ( $fields as &$field ) {

			switch ( $field->inputName ) {

				case 'job_category':
					$field->choices = $this->get_tax_term_choices( 'job_category' );
					break;

				case 'job_type':
					$field->choices = $this->get_tax_term_choices( 'job_type' );
					break;
			}

		}

		return $form;
	}
}

WPCampus_Jobs_Global::register();