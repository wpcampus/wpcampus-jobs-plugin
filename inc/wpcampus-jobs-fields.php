<?php

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

acf_add_local_field_group(
	[
		'key'                   => 'group_5ec99a920677f',
		'title'                 => 'Job forms',
		'fields'                => [
			[
				'key'               => 'field_5ec99a9b34dd7',
				'label'             => 'Submit job posting form',
				'name'              => 'wpc_submit_job_form',
				'type'              => 'select',
				'instructions'      => 'Which form is used to submit job postings?',
				'required'          => 0,
				'conditional_logic' => 0,
				'choices'           => [],
				'default_value'     => false,
				'allow_null'        => 1,
				'multiple'          => 0,
				'ui'                => 1,
				'ajax'              => 0,
				'return_format'     => 'value',
				'placeholder'       => '',
			],
			[
				'key'               => 'field_5ec99b6bde213',
				'label'             => 'Edit job posting form',
				'name'              => 'wpc_edit_job_form',
				'type'              => 'select',
				'instructions'      => 'Which form is used to edit job postings?',
				'required'          => 0,
				'conditional_logic' => 0,
				'choices'           => [],
				'default_value'     => false,
				'allow_null'        => 1,
				'multiple'          => 0,
				'ui'                => 1,
				'ajax'              => 0,
				'return_format'     => 'value',
				'placeholder'       => '',
			],
		],
		'location'              => [
			[
				[
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => 'wpcampus-jobs-settings',
				],
			],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'left',
		'instruction_placement' => 'field',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	]
);

acf_add_local_field_group(
	[
		'key'                   => 'group_5ec9698e727d1',
		'title'                 => 'About the job',
		'fields'                => [
			[
				'key'               => 'field_5ec96a2a7f6be',
				'label'             => 'Job location',
				'name'              => 'job_location',
				'type'              => 'text',
				'instructions'      => 'Where would this job be physically located?',
				'required'          => 0,
				'conditional_logic' => 0,
				'default_value'     => '',
				'placeholder'       => 'Leave blank if this job does not have a physical location.',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			],
			[
				'key'               => 'field_5ec96a617f6bf',
				'label'             => 'URL for Job posting',
				'name'              => 'job_posting',
				'type'              => 'url',
				'instructions'      => 'Provide a URL for the official job posting.',
				'required'          => 1,
				'conditional_logic' => 0,
				'default_value'     => '',
				'placeholder'       => 'https://...',
			],
			[
				'key'               => 'field_5ec96aa07f6c0',
				'label'             => 'Date the job was posted',
				'name'              => 'job_posted_date',
				'type'              => 'date_picker',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'display_format'    => 'm/d/Y',
				'return_format'     => 'm/d/Y',
				'first_day'         => 1,
			],
			[
				'key'               => 'field_5ec96ac17f6c1',
				'label'             => 'Date the job posting will close',
				'name'              => 'job_closing_date',
				'type'              => 'date_picker',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'display_format'    => 'd/m/Y',
				'return_format'     => 'd/m/Y',
				'first_day'         => 1,
			],
			[
				'key'               => 'field_5ec96c6db73c8',
				'label'             => 'Job type',
				'name'              => 'job_type',
				'type'              => 'taxonomy',
				'instructions'      => 'Choose all possible options.',
				'required'          => 1,
				'conditional_logic' => 0,
				'taxonomy'          => 'job_type',
				'field_type'        => 'checkbox',
				'add_term'          => 0,
				'save_terms'        => 1,
				'load_terms'        => 1,
				'return_format'     => 'id',
				'multiple'          => 0,
				'allow_null'        => 0,
			],
			[
				'key'               => 'field_5ec9700761316',
				'label'             => 'Job categories',
				'name'              => 'job_category',
				'type'              => 'taxonomy',
				'instructions'      => 'Choose all possible options.',
				'required'          => 1,
				'conditional_logic' => 0,
				'taxonomy'          => 'job_category',
				'field_type'        => 'checkbox',
				'add_term'          => 1,
				'save_terms'        => 1,
				'load_terms'        => 1,
				'return_format'     => 'id',
				'multiple'          => 0,
				'allow_null'        => 0,
			],
		],
		'location'              => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'job',
				],
			],
		],
		'menu_order'            => 1,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'left',
		'instruction_placement' => 'field',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	]
);

acf_add_local_field_group(
	[
		'key'                   => 'group_5ec9706982b78',
		'title'                 => 'About the organization',
		'fields'                => [
			[
				'key'               => 'field_5ec970893a4e3',
				'label'             => 'Organization name',
				'name'              => 'org_name',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'default_value'     => '',
				'placeholder'       => 'What is the name of the hiring organization?',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			],
			[
				'key'               => 'field_5ec970a93a4e4',
				'label'             => 'Organization tagline',
				'name'              => 'org_tagline',
				'type'              => 'wysiwyg',
				'instructions'      => 'Briefly describe the organization.',
				'required'          => 0,
				'conditional_logic' => 0,
				'default_value'     => '',
				'tabs'              => 'text',
				'media_upload'      => 0,
				'toolbar'           => 'full',
				'delay'             => 0,
			],
			[
				'key'               => 'field_5ec970e8f8a78',
				'label'             => 'Organization website',
				'name'              => 'org_website',
				'type'              => 'url',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'default_value'     => '',
				'placeholder'       => 'https://...',
			],
			/*[ @TODO is post featured image?
				'key'               => 'field_5ec9716cf8a7b',
				'label'             => 'Organization logo',
				'name'              => 'org_logo',
				'type'              => 'image',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'return_format'     => 'id',
				'preview_size'      => 'medium',
				'library'           => 'all',
				'min_width'         => '',
				'min_height'        => '',
				'min_size'          => '',
				'max_width'         => '',
				'max_height'        => '',
				'max_size'          => 24,
				'mime_types'        => 'jpg,png,svg',
			],*/
			[
				'key'               => 'field_5ec97126f8a7a',
				'label'             => 'Organization twitter username',
				'name'              => 'org_twitter',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'default_value'     => '',
				'placeholder'       => 'The organization\'s Twitter username',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			],
			[
				'key'               => 'field_5ec970fbf8a79',
				'label'             => 'Organization video',
				'name'              => 'org_video',
				'type'              => 'url',
				'instructions'      => 'Include a promotional video about your organization.',
				'required'          => 0,
				'conditional_logic' => 0,
				'default_value'     => '',
				'placeholder'       => 'A YouTube or Vimeo URL',
			],
		],
		'location'              => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'job',
				],
			],
		],
		'menu_order'            => 2,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'left',
		'instruction_placement' => 'field',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	]
);