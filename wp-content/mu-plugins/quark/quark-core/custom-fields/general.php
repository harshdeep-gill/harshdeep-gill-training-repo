<?php
/**
 * Options: General.
 *
 * @package quark-core
 */

/**
 * Register field group.
 */
if ( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group(
		[
			'key'                   => 'group_89f587b8b295d',
			'title'                 => 'General',
			'fields'                => [
				[
					'key'               => 'field_6799f6c16258a',
					'label'             => 'Display Site-wide Banner Message',
					'name'              => 'display_site_wide_banner_message',
					'aria-label'        => '',
					'type'              => 'true_false',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'message'           => '',
					'default_value'     => 0,
					'allow_in_bindings' => 0,
					'ui'                => 0,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				],
				[
					'key'               => 'field_34f587b9a2c71',
					'label'             => 'Site-wide Banner Message',
					'name'              => 'site_wide_banner_message',
					'aria-label'        => '',
					'type'              => 'wysiwyg',
					'instructions'      => 'A message to be displayed at the top of every page on the site.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'allow_in_bindings' => 0,
					'tabs'              => 'visual',
					'toolbar'           => 'full',
					'media_upload'      => 0,
					'delay'             => 0,
				],
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'acf-options-general',
					],
				],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		]
	);
}
