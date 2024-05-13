<?php
/**
 * Custom fields: Adventure Options Category.
 *
 * @package quark-adventure-options
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for adventure option taxonomy.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65ee82e616e91',
			'title'                 => 'Adventure Options (Taxonomy)',
			'fields'                => [
				[
					'key'               => 'field_65ee83342b5a0',
					'label'             => 'Icon',
					'name'              => 'icon',
					'aria-label'        => '',
					'type'              => 'image',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'return_format'     => 'array',
					'library'           => 'uploadedTo',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
					'preview_size'      => 'medium',
				],
				[
					'key'               => 'field_6633a0ea8291a',
					'label'             => 'Softrip',
					'name'              => 'softrip',
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
					'layout'            => 'table',
					'pagination'        => 0,
					'min'               => 0,
					'max'               => 0,
					'collapsed'         => '',
					'button_label'      => 'Add ID',
					'rows_per_page'     => 20,
					'sub_fields'        => [
						[
							'key'               => 'field_6633a10d8291b',
							'label'             => 'ID',
							'name'              => 'id',
							'aria-label'        => '',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 1,
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
							'parent_repeater'   => 'field_6633a0ea8291a',
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'qrk_adventure_option_category',
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

	// End if condition.
endif;
