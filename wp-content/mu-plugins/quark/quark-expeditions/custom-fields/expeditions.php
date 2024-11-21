<?php
/**
 * Custom fields: Expedition.
 *
 * @package quark-expeditions
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Excursion Taxonomy.
	acf_add_local_field_group(
		[
			'key'                   => 'group_6690db15722d8',
			'title'                 => 'Expeditions',
			'fields'                => [
				[
					'key'                  => 'field_66910d27ee66b',
					'label'                => 'Included Activities',
					'name'                 => 'included_activities',
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
						0 => 'qrk_adventure_option',
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
					'bidirectional'        => 0,
					'bidirectional_target' => [],
				],
				[
					'key'                  => 'field_66910e1fee66c',
					'label'                => 'Related Adventure Options',
					'name'                 => 'related_adventure_options',
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
						0 => 'qrk_adventure_option',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'filters'              => [
						0 => 'search',
					],
					'return_format'        => 'object',
					'min'                  => '',
					'max'                  => '',
					'elements'             => '',
					'bidirectional'        => 0,
					'bidirectional_target' => [],
				],
				[
					'key'                  => 'field_66910e5dee66d',
					'label'                => 'Related Pre-Post Trip Options',
					'name'                 => 'related_pre_post_trips',
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
						0 => 'qrk_pre_post_trip',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'filters'              => [
						0 => 'search',
					],
					'return_format'        => 'object',
					'min'                  => '',
					'max'                  => '',
					'elements'             => '',
					'bidirectional'        => 0,
					'bidirectional_target' => [],
				],
				[
					'key'               => 'field_66910e9dee66e',
					'label'             => 'Overview',
					'name'              => 'overview',
					'aria-label'        => '',
					'type'              => 'wysiwyg',
					'instructions'      => 'Add text here that will be used as an overview in integrated applications',
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
					'media_upload'      => 0,
					'delay'             => 0,
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_expedition',
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
