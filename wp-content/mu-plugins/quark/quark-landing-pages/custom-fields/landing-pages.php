<?php
/**
 * Custom fields: Landing Pages.
 *
 * @package quark-landing-pages
 */

// Add custom fields.
if ( function_exists( 'acf_add_local_field_group' ) ) :
	acf_add_local_field_group(
		[
			'key'                   => 'group_6178ea51b4192',
			'title'                 => 'Landing Page Form Fields',
			'fields'                => [
				[
					'key'               => 'field_61a04b4f51d71',
					'label'             => 'Polar Region',
					'name'              => 'polar_region',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'default_value'     => '',
					'media_upload'      => 0,
				],
				[
					'key'               => 'field_61a04b4551d70',
					'label'             => 'Season',
					'name'              => 'season',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'default_value'     => '',
					'media_upload'      => 0,
				],
				[
					'key'               => 'field_6281f2db3d4c3',
					'label'             => 'Ship',
					'name'              => 'ship',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'default_value'     => '',
					'media_upload'      => 0,
				],
				[
					'key'               => 'field_6281f2db3d4c2',
					'label'             => 'Sub Region',
					'name'              => 'sub_region',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'default_value'     => '',
					'media_upload'      => 0,
				],
				[
					'key'               => 'field_6281f2db3d4c4',
					'label'             => 'Expedition',
					'name'              => 'expedition',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'default_value'     => '',
					'media_upload'      => 0,
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_landing_page',
					],
				],
			],
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		]
	);
endif;
