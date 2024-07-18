<?php
/**
 * Custom fields: Inclusion / Exclusion Set Taxonomy.
 *
 * @package quark-inclusion-sets
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Inclusion / Exclusion Set Taxonomy.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65f2cf674e37c',
			'title'                 => 'Inclusion / Exclusion Set',
			'fields'                => [
				[
					'key'               => 'field_65f2cf6773073',
					'label'             => 'Display Title',
					'name'              => 'display_title',
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
				],
				[
					'key'               => 'field_65f2d5f02d5b4',
					'label'             => 'Set',
					'name'              => 'set',
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
					'button_label'      => 'Add Row',
					'rows_per_page'     => 20,
					'sub_fields'        => [
						[
							'key'               => 'field_65f2d6762d5b5',
							'label'             => 'Item',
							'name'              => 'item',
							'aria-label'        => '',
							'type'              => 'wysiwyg',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'tabs'              => 'all',
							'toolbar'           => 'basic',
							'media_upload'      => 0,
							'delay'             => 1,
							'parent_repeater'   => 'field_65f2d5f02d5b4',
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_inclusion_set',
					],
				],
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_exclusion_set',
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
