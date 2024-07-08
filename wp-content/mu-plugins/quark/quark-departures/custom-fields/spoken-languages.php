<?php
/**
 * Custom fields: Spoken Language Taxonomy.
 *
 * @package quark-departures
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Spoken Language.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65ee994edfe8f',
			'title'                 => 'Languages',
			'fields'                => [
				[
					'key'               => 'field_65ee994f674ed',
					'label'             => 'Language Code',
					'name'              => 'language_code',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Input the language code corresponding to the text. Example: \'en\' for English, \'es\' for Spanish.',
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
			],
			'location'              => [
				[
					[
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'qrk_spoken_language',
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
