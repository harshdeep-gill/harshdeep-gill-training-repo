<?php
/**
 * Custom fields: Blog.
 *
 * @package quark-blog
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Blog CPT.
	acf_add_local_field_group(
		[
			'key'                   => 'group_66336356e67e1',
			'title'                 => 'Posts',
			'fields'                => [
				[
					'key'                  => 'field_6633635709854',
					'label'                => 'Blog Authors',
					'name'                 => 'blog_authors',
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
						0 => 'qrk_blog_author',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'filters'              => [
						0 => 'search',
					],
					'return_format'        => 'object',
					'min'                  => 0,
					'max'                  => '',
					'elements'             => '',
					'bidirectional'        => 1,
					'bidirectional_target' => [
						0 => 'field_66336a1bd6c9d',
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					],
				],
			],
			'menu_order'            => 1,
			'position'              => 'acf_after_title',
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
