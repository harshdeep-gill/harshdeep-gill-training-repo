<?php
/**
 * Custom fields: Ship Deck CPT.
 *
 * @package quark-ship-decks
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Ship Deck POST.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65f4428de5c60',
			'title'                 => 'Ship Deck',
			'fields'                => [
				[
					'key'               => 'field_65f4428e1a86a',
					'label'             => 'Deck Name',
					'name'              => 'deck_name',
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
					'key'               => 'field_65fc400679f5d',
					'label'             => 'Deck Plan Image',
					'name'              => 'deck_plan_image',
					'aria-label'        => '',
					'type'              => 'image',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'return_format'     => 'array',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
					'preview_size'      => 'medium_large',
				],
				[
					'key'               => 'field_65fc401b79f5e',
					'label'             => 'Vertical Deck Plan Image',
					'name'              => 'vertical_deck_plan_image',
					'aria-label'        => '',
					'type'              => 'image',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'return_format'     => 'array',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
					'preview_size'      => 'medium_large',
				],
				[
					'key'                  => 'field_65f444961a86e',
					'label'                => 'Cabin Categories',
					'name'                 => 'cabin_categories',
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
						0 => 'cabin_category',
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
					'key'               => 'field_66339a17ba0b0',
					'label'             => 'Public Spaces',
					'name'              => 'public_spaces',
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
							'key'               => 'field_66339a3aba0b1',
							'label'             => 'Title',
							'name'              => 'title',
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
							'parent_repeater'   => 'field_66339a17ba0b0',
						],
						[
							'key'               => 'field_66339a5cba0b2',
							'label'             => 'Image',
							'name'              => 'image',
							'aria-label'        => '',
							'type'              => 'image',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '',
								'class' => '',
								'id'    => '',
							],
							'return_format'     => 'array',
							'library'           => 'all',
							'min_width'         => '',
							'min_height'        => '',
							'min_size'          => '',
							'max_width'         => '',
							'max_height'        => '',
							'max_size'          => '',
							'mime_types'        => '',
							'preview_size'      => 'medium',
							'parent_repeater'   => 'field_66339a17ba0b0',
						],
						[
							'key'               => 'field_66339aa1ba0b3',
							'label'             => 'Description',
							'name'              => 'description',
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
							'parent_repeater'   => 'field_66339a17ba0b0',
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_ship_deck',
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
