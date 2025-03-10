<?php
/**
 * Custom fields: Itinerary posts.
 *
 * @package quark-itineraries
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Itinerary CPT.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65f2da00560ba',
			'title'                 => 'Itinerary',
			'fields'                => [
				[
					'key'               => 'field_65f2da00046dc',
					'label'             => 'Boilerplate',
					'name'              => 'boilerplate',
					'aria-label'        => '',
					'type'              => 'wysiwyg',
					'instructions'      => 'Provide a standardized template or description for the itinerary.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'tabs'              => 'all',
					'toolbar'           => 'full',
					'media_upload'      => 1,
					'delay'             => 0,
				],
				[
					'key'               => 'field_65f2da9e046de',
					'label'             => 'Softrip Package Code',
					'name'              => 'softrip_package_code',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Softrip package code for this itinerary.',
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
					'key'                  => 'field_65f2dab2046df',
					'label'                => 'Related Expedition',
					'name'                 => 'related_expedition',
					'aria-label'           => '',
					'type'                 => 'post_object',
					'instructions'         => 'Select the expedition linked to this itinerary.',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_expedition',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'return_format'        => 'object',
					'multiple'             => 0,
					'allow_null'           => 1,
					'ui'                   => 1,
					'bidirectional'        => 0,
					'bidirectional_target' => [],
				],
				[
					'key'               => 'field_65f2db53046e1',
					'label'             => 'Duration In Days',
					'name'              => 'duration_in_days',
					'aria-label'        => '',
					'type'              => 'number',
					'instructions'      => 'The length of the expedition, in days.',
					'required'          => 1,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'min'               => 1,
					'max'               => '',
					'placeholder'       => '',
					'step'              => '',
					'prepend'           => '',
					'append'            => '',
				],
				[
					'key'                  => 'field_65f2db67046e2',
					'label'                => 'Start Location',
					'name'                 => 'start_location',
					'aria-label'           => '',
					'type'                 => 'taxonomy',
					'instructions'         => 'The location where this itinerary begins.',
					'required'             => 1,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'taxonomy'             => 'qrk_departure_location',
					'add_term'             => 0,
					'save_terms'           => 0,
					'load_terms'           => 0,
					'return_format'        => 'object',
					'field_type'           => 'select',
					'allow_null'           => 0,
					'bidirectional'        => 0,
					'multiple'             => 0,
					'bidirectional_target' => [],
				],
				[
					'key'                  => 'field_65f2dbc7046e3',
					'label'                => 'End Location',
					'name'                 => 'end_location',
					'aria-label'           => '',
					'type'                 => 'taxonomy',
					'instructions'         => 'The location where this itinerary ends.',
					'required'             => 1,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'taxonomy'             => 'qrk_departure_location',
					'add_term'             => 0,
					'save_terms'           => 0,
					'load_terms'           => 0,
					'return_format'        => 'object',
					'field_type'           => 'select',
					'allow_null'           => 0,
					'bidirectional'        => 0,
					'multiple'             => 0,
					'bidirectional_target' => [],
				],
				[
					'key'                  => 'field_65f2dbe0046e4',
					'label'                => 'Embarkation Port',
					'name'                 => 'embarkation_port',
					'aria-label'           => '',
					'type'                 => 'post_object',
					'instructions'         => 'The port where the journey for this itinerary begins.',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_port',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'return_format'        => 'object',
					'multiple'             => 0,
					'allow_null'           => 0,
					'bidirectional'        => 0,
					'ui'                   => 1,
					'bidirectional_target' => [],
				],
				[
					'key'                  => 'field_65fc33bfa6f2c',
					'label'                => 'Disembarkation Port',
					'name'                 => 'disembarkation_port',
					'aria-label'           => '',
					'type'                 => 'post_object',
					'instructions'         => 'The port where the journey for this itinerary ends.',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_port',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'return_format'        => 'object',
					'multiple'             => 0,
					'allow_null'           => 0,
					'bidirectional'        => 0,
					'ui'                   => 1,
					'bidirectional_target' => [],
				],
				[
					'key'                  => 'field_65fc528964478',
					'label'                => 'Brochure / Dossier',
					'name'                 => 'brochure',
					'aria-label'           => '',
					'type'                 => 'post_object',
					'instructions'         => '',
					'required'             => 1,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_brochure',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'return_format'        => 'object',
					'multiple'             => 0,
					'allow_null'           => 0,
					'bidirectional'        => 0,
					'ui'                   => 1,
					'bidirectional_target' => [],
				],
				[
					'key'               => 'field_65fc52f46eb0b',
					'label'             => 'Map',
					'name'              => 'map',
					'aria-label'        => '',
					'type'              => 'image',
					'instructions'      => '',
					'required'          => 0,
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
					'preview_size'      => 'medium',
				],
				[
					'key'                  => 'field_66056030d26b9',
					'label'                => 'Inclusions',
					'name'                 => 'inclusions',
					'aria-label'           => '',
					'type'                 => 'relationship',
					'instructions'         => 'Select inclusion sets for itinerary.',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_inclusion_set',
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
					'key'                  => 'field_66055fc0d26b8',
					'label'                => 'Exclusions',
					'name'                 => 'exclusions',
					'aria-label'           => '',
					'type'                 => 'relationship',
					'instructions'         => 'Select exclusion sets for itinerary.',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_exclusion_set',
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
					'key'                  => 'field_65fc3197a3075',
					'label'                => 'Itinerary Days',
					'name'                 => 'itinerary_days',
					'aria-label'           => '',
					'type'                 => 'relationship',
					'instructions'         => '',
					'required'             => 1,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_itinerary_day',
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
					'key'               => 'field_65f2da9e046z',
					'label'             => 'Mandatory Transfer Title',
					'name'              => 'mandatory_transfer_title',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Mandatory Transfer Package Title for the Drawer',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => 'Mandatory Transfer Package',
					'maxlength'         => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				],
				[
					'key'               => 'field_65f2eac1de5b2',
					'label'             => 'Mandatory Transfer Price',
					'name'              => 'mandatory_transfer_price',
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
							'key'               => 'field_65f2eadbde5b3',
							'label'             => 'Mandatory Transfer Price USD',
							'name'              => 'usd',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'USD',
						],
						[
							'key'               => 'field_65f2eb1ede5b4',
							'label'             => 'Mandatory Transfer Price CAD',
							'name'              => 'cad',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'CAD',
						],
						[
							'key'               => 'field_65f2eb50de5b5',
							'label'             => 'Mandatory Transfer Price GBP',
							'name'              => 'gbp',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'GBP',
						],
						[
							'key'               => 'field_65f2eb61de5b6',
							'label'             => 'Mandatory Transfer Price AUD',
							'name'              => 'aud',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'AUD',
						],
						[
							'key'               => 'field_65f2eb78de5b7',
							'label'             => 'Mandatory Transfer Price EUR',
							'name'              => 'eur',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '0',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'EUR',
						],
					],
				],
				[
					'key'               => 'field_65f2da9e046f',
					'label'             => 'Offer Inclusion Text',
					'name'              => 'offer_inclusion_text',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Offer Inclusion Text to overwrite default copy - "Incl. Transfer Package"',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => [
						'width' => '',
						'class' => '',
						'id'    => '',
					],
					'default_value'     => '',
					'maxlength'         => '33',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
				],
				[
					'key'                  => 'field_65f2eb8fde5b8',
					'label'                => 'Mandatory Transfer Package Inclusion',
					'name'                 => 'mandatory_transfer_package_inclusion',
					'aria-label'           => '',
					'type'                 => 'post_object',
					'instructions'         => '',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_inclusion_set',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'return_format'        => 'object',
					'multiple'             => 0,
					'allow_null'           => 0,
					'bidirectional'        => 0,
					'ui'                   => 1,
					'bidirectional_target' => [],
				],
				[
					'key'                  => 'field_65f2ebbade5b9',
					'label'                => 'Mandatory Transfer Package Exclusion',
					'name'                 => 'mandatory_transfer_package_exclusion',
					'aria-label'           => '',
					'type'                 => 'post_object',
					'instructions'         => '',
					'required'             => 0,
					'conditional_logic'    => 0,
					'wrapper'              => [
						'width' => '50',
						'class' => '',
						'id'    => '',
					],
					'post_type'            => [
						0 => 'qrk_exclusion_set',
					],
					'post_status'          => '',
					'taxonomy'             => '',
					'return_format'        => 'object',
					'multiple'             => 0,
					'allow_null'           => 0,
					'bidirectional'        => 0,
					'ui'                   => 1,
					'bidirectional_target' => [],
				],
				[
					'key'               => 'field_65f2ee5d96e8c',
					'label'             => 'Supplement Price',
					'name'              => 'supplement_price',
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
							'key'               => 'field_65f2ee6d96e8d',
							'label'             => 'Supplement Price - USD',
							'name'              => 'usd',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'USD',
						],
						[
							'key'               => 'field_65f2ee8e96e8e',
							'label'             => 'Supplement Price / COVID Test Fee - CAD',
							'name'              => 'supplement_price__covid_test_fee_cad',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'CAD',
						],
						[
							'key'               => 'field_65f2eeb596e8f',
							'label'             => 'Supplement Price / COVID Test Fee - GBP',
							'name'              => 'supplement_price__covid_test_fee_gbp',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'GBP',
						],
						[
							'key'               => 'field_65f2eed596e90',
							'label'             => 'Supplement Price / COVID Test Fee - AUD',
							'name'              => 'supplement_price__covid_test_fee_aud',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'AUD',
						],
						[
							'key'               => 'field_65f2eeec96e91',
							'label'             => 'Supplement Price / COVID Test Fee - EUR',
							'name'              => 'supplement_price__covid_test_fee_eur',
							'aria-label'        => '',
							'type'              => 'number',
							'instructions'      => 'Format: 9.99',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'min'               => '',
							'max'               => '',
							'placeholder'       => '',
							'step'              => '',
							'prepend'           => '',
							'append'            => 'EUR',
						],
					],
				],
				[
					'key'               => 'field_65f2ef0b96e92',
					'label'             => 'Terms and Conditions and Policies',
					'name'              => 'tnc',
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
							'key'                  => 'field_65f2f04d96e93',
							'label'                => 'Cancellation Policy',
							'name'                 => 'cancellation_policy',
							'aria-label'           => '',
							'type'                 => 'post_object',
							'instructions'         => '',
							'required'             => 0,
							'conditional_logic'    => 0,
							'wrapper'              => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'post_type'            => [
								0 => 'qrk_agreement',
							],
							'post_status'          => '',
							'taxonomy'             => '',
							'return_format'        => 'id',
							'multiple'             => 0,
							'allow_null'           => 0,
							'bidirectional'        => 0,
							'ui'                   => 1,
							'bidirectional_target' => [],
						],
						[
							'key'                  => 'field_65f2f06b96e94',
							'label'                => 'Terms and Conditions',
							'name'                 => 'terms_and_conditions',
							'aria-label'           => '',
							'type'                 => 'post_object',
							'instructions'         => '',
							'required'             => 0,
							'conditional_logic'    => 0,
							'wrapper'              => [
								'width' => '50',
								'class' => '',
								'id'    => '',
							],
							'post_type'            => [
								0 => 'qrk_agreement',
							],
							'post_status'          => '',
							'taxonomy'             => '',
							'return_format'        => 'id',
							'multiple'             => 0,
							'allow_null'           => 0,
							'bidirectional'        => 0,
							'ui'                   => 1,
							'bidirectional_target' => [],
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_itinerary',
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
