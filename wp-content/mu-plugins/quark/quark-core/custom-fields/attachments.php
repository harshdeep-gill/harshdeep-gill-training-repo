<?php
/**
 * Custom fields: Attachments.
 *
 * @package quark-core
 */

/**
 * Register field group.
 */
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add custom field for Media files.
	acf_add_local_field_group(
		[
			'key'                   => 'group_662f926c4738d',
			'title'                 => 'Attachments',
			'fields'                => [
				[
					'key'               => 'field_662f926cabd81',
					'label'             => 'Photographer Credit',
					'name'              => 'photographer_credit',
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
			],
			'location'              => [
				[
					[
						'param'    => 'attachment',
						'operator' => '==',
						'value'    => 'image',
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
