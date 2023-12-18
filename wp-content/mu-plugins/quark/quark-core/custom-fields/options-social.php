<?php
/**
 * Options: Social.
 *
 * @package quark-core
 */

/**
 * Register field group.
 */
if ( function_exists( 'acf_add_local_field_group' ) ) :
	acf_add_local_field_group(
		[
			'key'                   => 'group_6371d1b86b5e6',
			'title'                 => 'Options: Social',
			'fields'                => [
				[
					'key'               => 'field_6371d1b89cb3c',
					'label'             => 'Facebook URL',
					'name'              => 'facebook_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => '',
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
					'key'               => 'field_6371d1b89cb3d',
					'label'             => 'Twitter URL',
					'name'              => 'twitter_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => '',
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
					'key'               => 'field_6371d1b89cb3e',
					'label'             => 'Instagram URL',
					'name'              => 'instagram_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => '',
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
					'key'               => 'field_6371d1b89cb3f',
					'label'             => 'Pinterest URL',
					'name'              => 'pinterest_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => '',
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
					'key'               => 'field_6371d1b89cb3g',
					'label'             => 'YouTube URL',
					'name'              => 'youtube_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => '',
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
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'acf-options-social',
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
