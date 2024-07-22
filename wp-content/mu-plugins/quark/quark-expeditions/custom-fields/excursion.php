<?php
/**
 * Custom fields: Excursion Taxonomy.
 *
 * @package quark-expeditions
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Excursion Taxonomy.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65ee85a7a65b3',
			'title'                 => 'Departure Destinations',
			'fields'                => [
				[
					'key'               => 'field_65ee85a7e5c7e',
					'label'             => 'Ship Description',
					'name'              => 'description',
					'aria-label'        => '',
					'type'              => 'wysiwyg',
					'instructions'      => 'Ship Website Description',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'tabs'              => 'all',
					'toolbar'           => 'full',
					'media_upload'      => 1,
					'delay'             => 0,
				],
				[
					'key'               => 'field_65ee86e6e5c80',
					'label'             => 'Latitude',
					'name'              => 'latitude',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Enter either in decimal 51.47879 or sexagesimal format 51° 28\' 43.644"',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'maxlength'         => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				],
				[
					'key'               => 'field_65ee8706e5c81',
					'label'             => 'Longitude',
					'name'              => 'longitude',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Enter either in decimal -0.010677 or sexagesimal format -0° 38.4372"',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'maxlength'         => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				],
				[
					'key'               => 'field_65ee87dae5c82',
					'label'             => 'Image',
					'name'              => 'image',
					'aria-label'        => '',
					'type'              => 'image',
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
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
					'preview_size'      => 'medium',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'qrk_excursion',
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
		],
	);

	// End if condition.
endif;
