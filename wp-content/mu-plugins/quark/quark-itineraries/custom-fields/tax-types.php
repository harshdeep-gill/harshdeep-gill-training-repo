<?php
/**
 * Custom fields: Tax Type Taxonomy.
 *
 * @package quark-itineraries
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Tax Type taxonomy.
	acf_add_local_field_group(
		[
			'key'                   => 'group_66f242855f504',
			'title'                 => 'Tax Types',
			'fields'                => [
				[
					'key'               => 'field_66f2428509e1f',
					'label'             => 'Tax Type',
					'name'              => 'tax_type',
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
					'allow_in_bindings' => 0,
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				],
				[
					'key'               => 'field_66f2432209e20',
					'label'             => 'Rate',
					'name'              => 'rate',
					'aria-label'        => '',
					'type'              => 'number',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'min'               => '',
					'max'               => '',
					'allow_in_bindings' => 0,
					'placeholder'       => '',
					'step'              => '',
					'prepend'           => '',
					'append'            => '',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'qrk_tax_type',
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
