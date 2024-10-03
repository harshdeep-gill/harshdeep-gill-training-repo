<?php
/**
 * Custom fields: Sort Order.
 *
 * @package quark-cabin-categories
 */

// Use constants.
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;

// Check if ACF function exists.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add field.
	acf_add_local_field_group(
		[
			'key'                   => 'group_657ace22709be',
			'title'                 => 'Sort Order',
			'fields'                => [
				[
					'key'               => 'field_657ace22ca459',
					'label'             => 'Sort Priority',
					'name'              => 'sort_priority',
					'type'              => 'number',
					'instructions'      => 'Enter the priority of the term. The higher the number, the higher the priority.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => 0,
					'min'               => 0,
					'max'               => '',
					'placeholder'       => 'Enter the priority',
					'step'              => 1,
					'prepend'           => '',
					'append'            => '',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => CABIN_CLASS_TAXONOMY,
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

// End check.
endif;
