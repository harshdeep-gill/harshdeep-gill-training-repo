<?php
/**
 * Site Settings: Softrip.
 *
 * @package quark-softrip
 */

// Check if ACF is loaded.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Register fields.
	acf_add_local_field_group(
		[
			'key'                   => 'group_6658365084a7e',
			'title'                 => 'Softrip Settings',
			'fields'                => [
				[
					'key'               => 'field_665836511ee38',
					'label'             => 'Username',
					'name'              => 'softrip_username',
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
					'key'               => 'field_665836661ee39',
					'label'             => 'Password',
					'name'              => 'softrip_password',
					'aria-label'        => '',
					'type'              => 'password',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'acf-options-softrip',
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
