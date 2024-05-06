<?php
/**
 * Custom fields: Blog Authors.
 *
 * @package quark-blog-authors
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Blog Authors CPT.
	acf_add_local_field_group(
		[
			'key'                   => 'group_66336a1aedcf4',
			'title'                 => 'Blog Authors',
			'fields'                => [
				[
					'key'               => 'field_6633b319a7b66',
					'label'             => 'First Name',
					'name'              => 'first_name',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '50',
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
					'key'               => 'field_6633b32fa7b67',
					'label'             => 'Last Name',
					'name'              => 'last_name',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '50',
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
					'key'               => 'field_6633b349a7b68',
					'label'             => 'Author Title',
					'name'              => 'author_title',
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
					'key'               => 'field_6633b39fe1018',
					'label'             => 'Website URL',
					'name'              => 'website_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => 'This must be an external URL such as http://example.com.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'placeholder'       => '',
				],
				[
					'key'               => 'field_6633b3afe1019',
					'label'             => 'LinkedIn URL',
					'name'              => 'linkedin_url',
					'aria-label'        => '',
					'type'              => 'url',
					'instructions'      => 'This must be an external URL such as http://example.com.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'placeholder'       => '',
				],
				[
					'key'               => 'field_6633b3cbe101a',
					'label'             => 'Twitter Username',
					'name'              => 'twitter_username',
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
					'key'                  => 'field_66336a1bd6c9d',
					'label'                => 'Blog Posts',
					'name'                 => 'blog_posts',
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
						0 => 'post',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'filters'              => [
						0 => 'search',
					],
					'return_format'        => 'id',
					'min'                  => '',
					'max'                  => '',
					'elements'             => '',
					'bidirectional'        => 1,
					'bidirectional_target' => [
						0 => 'field_6633635709854',
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_blog_authors',
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
