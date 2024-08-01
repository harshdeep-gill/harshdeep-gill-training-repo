<?php
/**
 * Custom fields: Promotion Tag Taxonomy.
 *
 * @package quark-departures
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local field group for Promotion tags.
	acf_add_local_field_group(
		[
			'key'                   => 'group_66aa0760e63da',
			'title'                 => 'Promotion Tag',
			'fields'                => [
				[
					'key'                  => 'field_66aa0761dc671',
					'label'                => 'Related Departures',
					'name'                 => 'related_departures',
					'aria-label'           => '',
					'type'                 => 'relationship',
					'instructions'         => '',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_departure',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'filters'              => [
						0 => 'search',
					],
					'return_format'        => 'id',
					'min'                  => '',
					'max'                  => '',
					'elements'             => '',
					'bidirectional'        => 1,
					'bidirectional_target' => [
						0 => 'field_66aa10e42d4f5',
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'qrk_promotion_tags',
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
