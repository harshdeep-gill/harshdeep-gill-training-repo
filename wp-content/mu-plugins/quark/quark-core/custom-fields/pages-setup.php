<?php
/**
 * Options: Pages Setup.
 *
 * @package quark-core
 */

/**
 * Register field group.
 */
if ( function_exists( 'acf_add_local_field_group' ) ) :
	acf_add_local_field_group(
		[
			'key'                   => 'group_63f587b8b295d',
			'title'                 => 'Pages Setup',
			'fields'                => [
				[
					'key'               => 'field_63f51221119a2c79',
					'label'             => 'Arctic Destinations Page',
					'name'              => 'arctic_destinations_page',
					'aria-label'        => '',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'         => [
						0 => 'page',
					],
					'taxonomy'          => '',
					'return_format'     => 'object',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 1,
				],
				[
					'key'               => 'field_63f587b9a2c76',
					'label'             => 'Antarctic Destinations Page',
					'name'              => 'antarctic_destinations_page',
					'aria-label'        => '',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'         => [
						0 => 'page',
					],
					'taxonomy'          => '',
					'return_format'     => 'object',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 1,
				],
				[
					'key'               => 'field_63f587b9a2c77',
					'label'             => 'Press Releases Page',
					'name'              => 'press_releases_page',
					'aria-label'        => '',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'         => [
						0 => 'page',
					],
					'taxonomy'          => '',
					'return_format'     => 'object',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 1,
				],
				[
					'key'               => 'field_63f587b9a2c78',
					'label'             => 'Staff Members Page',
					'name'              => 'staff_members_page',
					'aria-label'        => '',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'         => [
						0 => 'page',
					],
					'taxonomy'          => '',
					'return_format'     => 'object',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 1,
				],
				[
					'key'               => 'field_63f587b9a2c74',
					'label'             => 'Ships Page',
					'name'              => 'ships_page',
					'aria-label'        => '',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'         => [
						0 => 'page',
					],
					'taxonomy'          => '',
					'return_format'     => 'object',
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 1,
				],
				[
					'key'               => 'field_63f587b9a2c75',
					'label'             => 'Adventure Options Page',
					'name'              => 'adventure_options_page',
					'aria-label'        => '',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'         => [
						0 => 'page',
					],
					'taxonomy'          => '',
					'return_format'     => 'object',
					'multiple'          => 0,
					'allow_null'        => 1,
					'ui'                => 1,
				],
				[
					'key'               => 'field_63f587b9a2c79',
					'label'             => 'Expedition Search Page',
					'name'              => 'expedition_search_page',
					'aria-label'        => '',
					'type'              => 'post_object',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'         => [
						0 => 'page',
					],
					'taxonomy'          => '',
					'return_format'     => 'object',
					'multiple'          => 0,
					'allow_null'        => 1,
					'ui'                => 1,
				],
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'acf-options-pages-setup',
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
endif;
