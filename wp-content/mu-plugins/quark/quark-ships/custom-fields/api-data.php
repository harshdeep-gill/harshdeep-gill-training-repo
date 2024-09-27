<?php
/**
 * Custom fields: Ship API Data.
 *
 * @package quark-ships
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add custom fields for Ship API data.
	acf_add_local_field_group(
		[
			'key'                   => 'group_66f67291dc655',
			'title'                 => 'API Data',
			'fields'                => [
				[
					'key'               => 'field_66f6729260f93',
					'label'             => 'Cabin',
					'name'              => 'cabin',
					'aria-label'        => '',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'layout'            => 'block',
					'pagination'        => 0,
					'min'               => 0,
					'max'               => 0,
					'collapsed'         => '',
					'button_label'      => 'Add Row',
					'rows_per_page'     => 20,
					'sub_fields'        => [
						[
							'key'               => 'field_66f672ac60f94',
							'label'             => 'Item',
							'name'              => 'item',
							'aria-label'        => '',
							'type'              => 'textarea',
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
							'rows'              => 2,
							'placeholder'       => '',
							'new_lines'         => '',
							'parent_repeater'   => 'field_66f6729260f93',
						],
					],
				],
				[
					'key'               => 'field_66f672e160f95',
					'label'             => 'Aboard',
					'name'              => 'aboard',
					'aria-label'        => '',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'layout'            => 'block',
					'pagination'        => 0,
					'min'               => 0,
					'max'               => 0,
					'collapsed'         => '',
					'button_label'      => 'Add Row',
					'rows_per_page'     => 20,
					'sub_fields'        => [
						[
							'key'               => 'field_66f672e160f96',
							'label'             => 'Item',
							'name'              => 'item',
							'aria-label'        => '',
							'type'              => 'textarea',
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
							'rows'              => 2,
							'placeholder'       => '',
							'new_lines'         => '',
							'parent_repeater'   => 'field_66f672e160f95',
						],
					],
				],
				[
					'key'               => 'field_66f672f460f97',
					'label'             => 'Activities',
					'name'              => 'activities',
					'aria-label'        => '',
					'type'              => 'repeater',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'layout'            => 'block',
					'pagination'        => 0,
					'min'               => 0,
					'max'               => 0,
					'collapsed'         => '',
					'button_label'      => 'Add Row',
					'rows_per_page'     => 20,
					'sub_fields'        => [
						[
							'key'               => 'field_66f672f460f98',
							'label'             => 'Item',
							'name'              => 'item',
							'aria-label'        => '',
							'type'              => 'textarea',
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
							'rows'              => 2,
							'placeholder'       => '',
							'new_lines'         => '',
							'parent_repeater'   => 'field_66f672f460f97',
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_ship',
					],
				],
			],
			'menu_order'            => 110,
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

// End if function exists.
endif;
