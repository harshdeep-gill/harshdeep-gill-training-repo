<?php
/**
 * Custom fields: Policy Pages CPT.
 *
 * @package quark-policy-pages
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Policy Pages.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65f4524651463',
			'title'                 => 'Terms and Conditions / Policy',
			'fields'                => [
				[
					'key'               => 'field_65f4574f5e401',
					'label'             => 'Alternate Title',
					'name'              => 'alternate_title',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Shown in banners, callouts, and modals.',
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
					'key'               => 'field_65f45246513ab',
					'label'             => 'Categorization',
					'name'              => 'categorization',
					'aria-label'        => '',
					'type'              => 'group',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'layout'            => 'block',
					'sub_fields'        => [
						[
							'key'               => 'field_65f453190463c',
							'label'             => 'Agreement Type',
							'name'              => 'agreement_type',
							'aria-label'        => '',
							'type'              => 'radio',
							'instructions'      => '',
							'required'          => 1,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '',
								'class' => '',
								'id'    => '',
							],
							'choices'           => [
								'terms_conditions' => 'Terms and Conditions',
								'policy'           => 'Policy',
							],
							'default_value'     => '',
							'return_format'     => 'value',
							'allow_null'        => 0,
							'other_choice'      => 0,
							'layout'            => 'vertical',
							'save_other_choice' => 0,
						],
					],
				],
				[
					'key'               => 'field_65f454670463d',
					'label'             => 'Marketing Copy and Display Options',
					'name'              => 'marketing_options',
					'aria-label'        => '',
					'type'              => 'group',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'layout'            => 'block',
					'sub_fields'        => [
						[
							'key'               => 'field_65f454d30463e',
							'label'             => 'Icon',
							'name'              => 'icon',
							'aria-label'        => '',
							'type'              => 'image',
							'instructions'      => 'You can select one media item.',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '',
								'class' => '',
								'id'    => '',
							],
							'return_format'     => 'id',
							'library'           => 'all',
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
							'key'                       => 'field_65f455670463f',
							'label'                     => 'Agreement Display Options',
							'name'                      => 'display_options',
							'aria-label'                => '',
							'type'                      => 'checkbox',
							'instructions'              => 'Choose where these will display:
Expedition: TBD Itinerary: Within the itinerary tab as a link. Departure: At the top of each departure card.',
							'required'                  => 0,
							'conditional_logic'         => 0,
							'wrapper'                   => [
								'width' => '',
								'class' => '',
								'id'    => '',
							],
							'choices'                   => [
								'expedition' => 'Expedition (Note: Future option. Currently not implemented.)',
								'itinerary'  => 'Itinerary (Note: Future option. Currently not implemented.)',
								'departure'  => 'Departure',
							],
							'default_value'             => [],
							'return_format'             => 'value',
							'allow_custom'              => 0,
							'layout'                    => 'vertical',
							'toggle'                    => 0,
							'save_custom'               => 0,
							'custom_choice_button_text' => 'Add new choice',
						],
						[
							'key'               => 'field_65f4564004640',
							'label'             => 'Marketing Summary',
							'name'              => 'marketing_summary',
							'aria-label'        => '',
							'type'              => 'text',
							'instructions'      => 'A short marketing statement describing the policy, such as "Eligible for risk-free cancellation and flexible rebooking."',
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
							'key'               => 'field_65f4566604641',
							'label'             => 'Include in Content Migrations to Ships',
							'name'              => 'migrate_to_ship_portal',
							'aria-label'        => '',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '',
								'class' => '',
								'id'    => '',
							],
							'message'           => 'If checked this node (title, body, and alternate title) will be migrated to the ship websites.',
							'default_value'     => 0,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
							'ui'                => 1,
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_agreement',
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
