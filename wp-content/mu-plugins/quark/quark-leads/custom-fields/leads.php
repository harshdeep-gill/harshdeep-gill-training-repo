<?php
/**
 * Site Settings: Leads.
 *
 * @package quark-leads
 */

// Check if the function 'acf_add_local_field_group' exists.
if ( function_exists( 'acf_add_local_field_group' ) ) :
	// Add fields.
	acf_add_local_field_group(
		[
			'key'                   => 'group_6092179e8c279',
			'title'                 => 'Leads',
			'fields'                => [
				[
					'key'               => 'field_60caef8a96d32',
					'label'             => 'Validate reCAPTCHA',
					'name'              => 'validate_recaptcha',
					'type'              => 'true_false',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'message'           => '',
					'default_value'     => 1,
					'ui'                => 1,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				],
				[
					'key'               => 'field_60caefb596d33',
					'label'             => 'Allow reCAPTCHA to Fail',
					'name'              => 'allow_recaptcha_to_fail',
					'type'              => 'true_false',
					'instructions'      => 'Allow the user to submit forms even if reCAPTCHA fails.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'message'           => '',
					'default_value'     => 0,
					'ui'                => 1,
					'ui_on_text'        => '',
					'ui_off_text'       => '',
				],
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'acf-options-leads',
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
		]
	);
endif;
