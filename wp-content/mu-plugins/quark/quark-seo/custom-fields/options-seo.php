<?php
/**
 * Options: SEO
 *
 * @package quark-seo
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for SEO.
	acf_add_local_field_group(
		[
			'key'                   => 'group_64bd7e8119519',
			'title'                 => 'Options: SEO',
			'fields'                => [
				[
					'key'               => 'field_64bd7e8191511',
					'label'             => 'SEO Robots.txt',
					'name'              => 'seo_robots_txt',
					'aria-label'        => '',
					'type'              => 'textarea',
					'instructions'      => 'Preview - <a href="/robots.txt" target="_blank">Robots.txt</a>',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'maxlength'         => '',
					'rows'              => '',
					'placeholder'       => '',
					'new_lines'         => '',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'acf-options-seo',
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
