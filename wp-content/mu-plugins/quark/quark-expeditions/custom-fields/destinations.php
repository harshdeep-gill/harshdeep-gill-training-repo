<?php
/**
 * Custom fields: Destinations Taxonomy.
 *
 * @package quark-expeditions
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Destination Taxonomy.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65ee94271a36b',
			'title'                 => 'Destinations',
			'fields'                => [
				[
					'key'               => 'field_65ee9427479ff',
					'label'             => 'Softrip ID',
					'name'              => 'softrip_id',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => '',
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
					'key'               => 'field_65ee94274a1b0',
					'label'             => 'Destination Image',
					'name'              => 'destination_image',
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
						'value'    => 'qrk_destination',
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
