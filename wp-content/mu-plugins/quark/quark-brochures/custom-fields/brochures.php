<?php
/**
 * Custom fields: Brochures.
 *
 * @package quark-brochures
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for brochures CPT.
	acf_add_local_field_group(
		[
			'key'                   => 'group_662f6f3aa7c16',
			'title'                 => 'Brochures',
			'fields'                => [
				[
					'key'               => 'field_662f6f3a35949',
					'label'             => 'Brochure PDF',
					'name'              => 'brochure_pdf',
					'aria-label'        => '',
					'type'              => 'file',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'return_format'     => 'array',
					'library'           => 'all',
					'min_size'          => '',
					'max_size'          => '',
					'mime_types'        => '',
				],
				[
					'key'               => 'field_662f70233594b',
					'label'             => 'External URL',
					'name'              => 'external_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => 'Enter the URL if PDF is hosted elsewhere.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'placeholder'       => '',
				],
				[
					'key'               => 'field_662f6fe43594a',
					'label'             => 'PDF Is Gated',
					'name'              => 'pdf_is_gated',
					'aria-label'        => '',
					'type'              => 'true_false',
					'instructions'      => 'Prompt a visitor to enter contact information before accessing this PDF.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'message'           => '',
					'default_value'     => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
					'ui'                => 1,
				],
				[
					'key'               => 'field_662f70463594c',
					'label'             => 'Season',
					'name'              => 'season',
					'aria-label'        => '',
					'type'              => 'date_picker',
					'instructions'      => 'The four-digit year that the season begins.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'display_format'    => 'Y',
					'return_format'     => 'Y',
					'first_day'         => 1,
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_brochures',
					],
				],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		]
	);

	// End if condition.
endif;
