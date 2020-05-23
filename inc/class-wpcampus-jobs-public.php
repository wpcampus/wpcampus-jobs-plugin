<?php

/**
 * The class that sets up public-facing plugin functionality.
 *
 * This class is initiated on every non-admin page
 * load and does not have to be instantiated.
 *
 * @class       WPCampus_Jobs_Public
 * @package     WPCampus_Jobs
 */
final class WPCampus_Jobs_Public {

	/**
	 * We don't need to instantiate this class.
	 */
	protected function __construct() { }

	/**
	 * Registers all of our hooks and what not.
	 */
	public static function register() {
		$plugin = new self();

		add_action( 'rest_api_init', [ $plugin, 'register_rest_routes' ] );

	}

	/**
	 * Register our custom REST routes.
	 */
	public function register_rest_routes() {

		register_rest_route(
			'wpcampus',
			'/jobs/',
			[
				'methods'  => 'GET',
				'callback' => [ $this, 'get_rest_jobs' ],
			]
		);
	}

	/**
	 * Return all terms assigned to jobs, sorted by job ID.
	 *
	 * @param array $jobs
	 *
	 * @return array
	 */
	private function get_job_terms( array $jobs ) {
		global $wpdb;

		if ( empty( $jobs ) ) {
			return [];
		}

		// Convert to job IDs.
		$job_ids = array_map( function ( $job ) {
			return ! empty( $job->ID ) ? $job->ID : '';
		}, $jobs );

		// Remove empty IDs.
		$job_ids = array_filter( $job_ids );

		if ( empty( $job_ids ) ) {
			return [];
		}

		$terms_query = "SELECT rel.object_id, terms.term_id, tax.taxonomy, terms.name, terms.slug FROM wp_term_relationships rel
			INNER JOIN wp_term_taxonomy tax ON tax.term_taxonomy_id = rel.term_taxonomy_id AND ( tax.taxonomy = 'job_type' OR tax.taxonomy = 'job_category' )
			INNER JOIN wp_terms terms ON terms.term_id = tax.term_id
			WHERE rel.object_id IN (" . implode( ',', $job_ids ) . ")
			ORDER BY terms.name ASC";

		$terms = $wpdb->get_results( $terms_query );

		if ( empty( $terms ) ) {
			return [];
		}

		// Index by job ID and taxonomy.
		$by_id = [];

		foreach ( $terms as $term ) {
			if ( empty( $term->object_id ) || empty( $term->taxonomy ) ) {
				continue;
			}

			// Store object ID and remove from term.
			$object_id = $term->object_id;
			unset( $term->object_id );

			// Make sure we have an index for the job ID.
			if ( ! isset( $by_id[ $object_id ] ) ) {
				$by_type[ $object_id ] = [];
			}

			// Make sure we have an index for the taxonomy.
			if ( ! isset( $by_id[ $object_id ][ $term->taxonomy ] ) ) {
				$by_id[ $object_id ][ $term->taxonomy ] = [];
			}

			$by_id[ $object_id ][ $term->taxonomy ][] = $term;
		}

		return $by_id;
	}

	/**
	 * Get jobs data for the endpoint response.
	 *
	 * @return array
	 */
	private function get_jobs() {
		global $wpdb;

		$jobs_query_inner = "( SELECT ID,
       		post_author AS author,
       		post_date AS date,
       		post_date_gmt AS date_gmt,
       		post_content AS content,
       		post_title AS title,
       		post_status AS status,
       		post_name AS slug,
       		post_modified AS modified,
       		post_modified_gmt AS modified_gmt,
       		null AS permalink
       		FROM {$wpdb->posts} WHERE post_type = 'job' AND post_status = 'publish') AS jobs";

		$meta_fields = [
			'job_location'     => 'job_location',
			'job_posting'      => 'job_posting',
			'job_posted_date'  => 'job_posted_date',
			'job_closing_date' => 'job_closing_date',
			'job_type'         => null,
			'job_category'     => null,
			'org_name'         => 'org_name',
			'org_tagline'      => 'org_tagline',
			'org_website'      => 'org_website',
			'org_twitter'      => 'org_twitter',
			'org_video'        => 'org_video',
			'org_logo_id'      => '_thumbnail_id',
			'org_logo'         => null,
		];

		$jobs_select = 'SELECT jobs.*';
		$jobs_join = '';

		foreach ( $meta_fields as $key => $column ) {

			// Add null to select.
			if ( empty( $column ) ) {
				$jobs_select .= ", null AS {$key}";
				continue;
			}

			$jobs_select .= ", {$key}.meta_value AS {$key}";
			$jobs_join .= $wpdb->prepare( " LEFT JOIN {$wpdb->postmeta} {$key} ON {$key}.post_id = jobs.ID AND {$key}.meta_key = %s", $column );

		}

		// Build final query.
		$jobs_query = $jobs_select . " FROM " . $jobs_query_inner . $jobs_join;

		$jobs = $wpdb->get_results( $jobs_query );

		if ( empty( $jobs ) ) {
			return [];
		}

		// Place job terms into appropriate job.
		$job_terms_by_id = $this->get_job_terms( $jobs );

		foreach ( $jobs as &$job ) {

			if ( isset( $job_terms_by_id[ $job->ID ] ) ) {
				$job_terms = $job_terms_by_id[ $job->ID ];
			} else {
				$job_terms = [];
			}

			$job->job_type = ! empty( $job_terms['job_type'] ) ? $job_terms['job_type'] : [];
			$job->job_category = ! empty( $job_terms['job_category'] ) ? $job_terms['job_category'] : [];

		}

		return $jobs;
	}

	/**
	 * Prepare rich text content for response.
	 *
	 * @TODO what tags do we allow?
	 *
	 * @param $content - string
	 *
	 * @return array
	 */
	private function prepare_content_for_response( $content ) {
		if ( ! empty( $content ) ) {
			$content = strip_tags( $content, '<p><br><em><strong>' );
		} else {
			$content = '';
		}
		return [
			'basic'    => $content,
			'rendered' => ! empty( $content ) ? wpautop( $content ) : '',
		];
	}

	/**
	 * Prepare a single job listing for the endpoint response.
	 *
	 * @param $job - object
	 *
	 * @return object
	 */
	private function prepare_job_for_response( $job ) {

		// Normalize job description.
		$job->content = $this->prepare_content_for_response( $job->content );

		// Normalize org tagline.
		$job->org_tagline = $this->prepare_content_for_response( $job->org_tagline );

		// Normalize org logo.
		if ( isset( $job->org_logo_id ) ) {

			if ( ! empty( $job->org_logo_id ) ) {

				// @TODO do we need full?
				$logo_attachment = wp_get_attachment_image_src( $job->org_logo_id, 'full' );

				// Add logo src.
				$job->org_logo = ! empty( $logo_attachment[0] ) ? $logo_attachment[0] : '';

			}

			unset( $job->org_logo_id );
		}

		// Normalize permalink.
		$job->permalink = get_permalink( $job->ID );

		return $job;
	}

	/**
	 * Prepare all job results for the endpoint respnose.
	 *
	 * @param $jobs - array
	 *
	 * @return array
	 */
	private function prepare_jobs_for_response( $jobs ) {
		if ( empty( $jobs ) ) {
			return [];
		}
		foreach ( $jobs as &$job ) {
			$job = $this->prepare_job_for_response( $job );
		}
		return $jobs;
	}

	/**
	 * Create response for jobs endpoint.
	 */
	public function get_rest_jobs() {

		$jobs = $this->get_jobs();

		if ( empty( $jobs ) ) {
			wp_send_json( [] );
			return;
		}

		wp_send_json( $this->prepare_jobs_for_response( $jobs ) );

	}
}

WPCampus_Jobs_Public::register();