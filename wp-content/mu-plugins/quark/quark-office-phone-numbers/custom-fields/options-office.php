<?php
/**
 * Options: Office.
 *
 * @package quark-office-phone-numbers
 */

/**
 * Register field group.
 */
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add group for Local Office contact details.
	acf_add_local_field_group(
		[
			'key'                   => 'group_6661b68a7ee89',
			'title'                 => 'Office',
			'fields'                => [
				[
					'key'               => 'field_6661bc6962d3d',
					'label'             => 'Country',
					'name'              => 'country',
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
					'collapsed'         => 'field_6661bcda62d3e',
					'button_label'      => 'Add Office Details',
					'rows_per_page'     => 20,
					'sub_fields'        => [
						[
							'key'               => 'field_6661bcda62d3e',
							'label'             => 'Name',
							'name'              => 'name',
							'aria-label'        => '',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '70',
								'class' => '',
								'id'    => '',
							],
							'default_value'     => '',
							'maxlength'         => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'parent_repeater'   => 'field_6661bc6962d3d',
						],
						[
							'key'               => 'field_6661bce662d3f',
							'label'             => 'Corporate Office',
							'name'              => 'corporate_office',
							'aria-label'        => '',
							'type'              => 'true_false',
							'instructions'      => 'Check if this is the corporate office.',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '30',
								'class' => '',
								'id'    => '',
							],
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 1,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
							'parent_repeater'   => 'field_6661bc6962d3d',
						],
						[
							'key'               => 'field_6661bcf162d3f',
							'label'             => 'Phone Number',
							'name'              => 'phone_number',
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
							'parent_repeater'   => 'field_6661bc6962d3d',
						],
						[
							'key'               => 'field_6661bd0662d40',
							'label'             => 'Phone Number Prefix',
							'name'              => 'phone_number_prefix',
							'aria-label'        => '',
							'type'              => 'text',
							'instructions'      => 'What to display before the phone number for the top navigation. For example: "Call".',
							'required'          => 0,
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
							'parent_repeater'   => 'field_6661bc6962d3d',
						],
						[
							'key'               => 'field_6661ceff73cf1',
							'label'             => 'Coverage / Target Country',
							'name'              => 'coverage',
							'aria-label'        => '',
							'type'              => 'select',
							'instructions'      => 'The country used to determine that this is the primary office to contact.',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => [
								'width' => '',
								'class' => '',
								'id'    => '',
							],
							'choices'           => [
								'AF' => 'Afghanistan',
								'AX' => 'Åland Islands',
								'AL' => 'Albania',
								'DZ' => 'Algeria',
								'AS' => 'American Samoa',
								'AD' => 'Andorra',
								'AO' => 'Angola',
								'AI' => 'Anguilla',
								'AQ' => 'Antarctica',
								'AG' => 'Antigua & Barbuda',
								'AR' => 'Argentina',
								'AM' => 'Armenia',
								'AW' => 'Aruba',
								'AC' => 'Ascension Island',
								'AU' => 'Australia',
								'AT' => 'Austria',
								'AZ' => 'Azerbaijan',
								'BS' => 'Bahamas',
								'BH' => 'Bahrain',
								'BD' => 'Bangladesh',
								'BB' => 'Barbados',
								'BY' => 'Belarus',
								'BE' => 'Belgium',
								'BZ' => 'Belize',
								'BJ' => 'Benin',
								'BM' => 'Bermuda',
								'BT' => 'Bhutan',
								'BO' => 'Bolivia',
								'BA' => 'Bosnia & Herzegovina',
								'BW' => 'Botswana',
								'BV' => 'Bouvet Island',
								'BR' => 'Brazil',
								'IO' => 'British Indian Ocean Territory',
								'VG' => 'British Virgin Islands',
								'BN' => 'Brunei',
								'BG' => 'Bulgaria',
								'BF' => 'Burkina Faso',
								'BI' => 'Burundi',
								'KH' => 'Cambodia',
								'CM' => 'Cameroon',
								'CA' => 'Canada',
								'IC' => 'Canary Islands',
								'CV' => 'Cape Verde',
								'BQ' => 'Caribbean Netherlands',
								'KY' => 'Cayman Islands',
								'CF' => 'Central African Republic',
								'EA' => 'Ceuta & Melilla',
								'TD' => 'Chad',
								'CL' => 'Chile',
								'CN' => 'China',
								'CX' => 'Christmas Island',
								'CP' => 'Clipperton Island',
								'CC' => 'Cocos (Keeling) Islands',
								'CO' => 'Colombia',
								'KM' => 'Comoros',
								'CG' => 'Congo - Brazzaville',
								'CD' => 'Congo - Kinshasa',
								'CK' => 'Cook Islands',
								'CR' => 'Costa Rica',
								'CI' => 'Côte d’Ivoire',
								'HR' => 'Croatia',
								'CU' => 'Cuba',
								'CW' => 'Curaçao',
								'CY' => 'Cyprus',
								'CZ' => 'Czechia',
								'DK' => 'Denmark',
								'DG' => 'Diego Garcia',
								'DJ' => 'Djibouti',
								'DM' => 'Dominica',
								'DO' => 'Dominican Republic',
								'EC' => 'Ecuador',
								'EG' => 'Egypt',
								'SV' => 'El Salvador',
								'GQ' => 'Equatorial Guinea',
								'ER' => 'Eritrea',
								'EE' => 'Estonia',
								'SZ' => 'Eswatini',
								'ET' => 'Ethiopia',
								'FK' => 'Falkland Islands',
								'FO' => 'Faroe Islands',
								'FJ' => 'Fiji',
								'FI' => 'Finland',
								'FR' => 'France',
								'GF' => 'French Guiana',
								'PF' => 'French Polynesia',
								'TF' => 'French Southern Territories',
								'GA' => 'Gabon',
								'GM' => 'Gambia',
								'GE' => 'Georgia',
								'DE' => 'Germany',
								'GH' => 'Ghana',
								'GI' => 'Gibraltar',
								'GR' => 'Greece',
								'GL' => 'Greenland',
								'GD' => 'Grenada',
								'GP' => 'Guadeloupe',
								'GU' => 'Guam',
								'GT' => 'Guatemala',
								'GG' => 'Guernsey',
								'GN' => 'Guinea',
								'GW' => 'Guinea-Bissau',
								'GY' => 'Guyana',
								'HT' => 'Haiti',
								'HM' => 'Heard & McDonald Islands',
								'HN' => 'Honduras',
								'HK' => 'Hong Kong SAR China',
								'HU' => 'Hungary',
								'IS' => 'Iceland',
								'IN' => 'India',
								'ID' => 'Indonesia',
								'IR' => 'Iran',
								'IQ' => 'Iraq',
								'IE' => 'Ireland',
								'IM' => 'Isle of Man',
								'IL' => 'Israel',
								'IT' => 'Italy',
								'JM' => 'Jamaica',
								'JP' => 'Japan',
								'JE' => 'Jersey',
								'JO' => 'Jordan',
								'KZ' => 'Kazakhstan',
								'KE' => 'Kenya',
								'KI' => 'Kiribati',
								'XK' => 'Kosovo',
								'KW' => 'Kuwait',
								'KG' => 'Kyrgyzstan',
								'LA' => 'Laos',
								'LV' => 'Latvia',
								'LB' => 'Lebanon',
								'LS' => 'Lesotho',
								'LR' => 'Liberia',
								'LY' => 'Libya',
								'LI' => 'Liechtenstein',
								'LT' => 'Lithuania',
								'LU' => 'Luxembourg',
								'MO' => 'Macao SAR China',
								'MG' => 'Madagascar',
								'MW' => 'Malawi',
								'MY' => 'Malaysia',
								'MV' => 'Maldives',
								'ML' => 'Mali',
								'MT' => 'Malta',
								'MH' => 'Marshall Islands',
								'MQ' => 'Martinique',
								'MR' => 'Mauritania',
								'MU' => 'Mauritius',
								'YT' => 'Mayotte',
								'MX' => 'Mexico',
								'FM' => 'Micronesia',
								'MD' => 'Moldova',
								'MC' => 'Monaco',
								'MN' => 'Mongolia',
								'ME' => 'Montenegro',
								'MS' => 'Montserrat',
								'MA' => 'Morocco',
								'MZ' => 'Mozambique',
								'MM' => 'Myanmar (Burma)',
								'NA' => 'Namibia',
								'NR' => 'Nauru',
								'NP' => 'Nepal',
								'NL' => 'Netherlands',
								'NC' => 'New Caledonia',
								'NZ' => 'New Zealand',
								'NI' => 'Nicaragua',
								'NE' => 'Niger',
								'NG' => 'Nigeria',
								'NU' => 'Niue',
								'NF' => 'Norfolk Island',
								'KP' => 'North Korea',
								'MK' => 'North Macedonia',
								'MP' => 'Northern Mariana Islands',
								'NO' => 'Norway',
								'OM' => 'Oman',
								'PK' => 'Pakistan',
								'PW' => 'Palau',
								'PS' => 'Palestinian Territories',
								'PA' => 'Panama',
								'PG' => 'Papua New Guinea',
								'PY' => 'Paraguay',
								'PE' => 'Peru',
								'PH' => 'Philippines',
								'PN' => 'Pitcairn Islands',
								'PL' => 'Poland',
								'PT' => 'Portugal',
								'PR' => 'Puerto Rico',
								'QA' => 'Qatar',
								'RE' => 'Réunion',
								'RO' => 'Romania',
								'RU' => 'Russia',
								'RW' => 'Rwanda',
								'WS' => 'Samoa',
								'SM' => 'San Marino',
								'ST' => 'São Tomé & Príncipe',
								'SA' => 'Saudi Arabia',
								'SN' => 'Senegal',
								'RS' => 'Serbia',
								'SC' => 'Seychelles',
								'SL' => 'Sierra Leone',
								'SG' => 'Singapore',
								'SX' => 'Sint Maarten',
								'SK' => 'Slovakia',
								'SI' => 'Slovenia',
								'SB' => 'Solomon Islands',
								'SO' => 'Somalia',
								'ZA' => 'South Africa',
								'GS' => 'South Georgia & South Sandwich Islands',
								'KR' => 'South Korea',
								'SS' => 'South Sudan',
								'ES' => 'Spain',
								'LK' => 'Sri Lanka',
								'BL' => 'St. Barthélemy',
								'SH' => 'St. Helena',
								'KN' => 'St. Kitts & Nevis',
								'LC' => 'St. Lucia',
								'MF' => 'St. Martin',
								'PM' => 'St. Pierre & Miquelon',
								'VC' => 'St. Vincent & Grenadines',
								'SD' => 'Sudan',
								'SR' => 'Suriname',
								'SJ' => 'Svalbard & Jan Mayen',
								'SE' => 'Sweden',
								'CH' => 'Switzerland',
								'SY' => 'Syria',
								'TW' => 'Taiwan',
								'TJ' => 'Tajikistan',
								'TZ' => 'Tanzania',
								'TH' => 'Thailand',
								'TL' => 'Timor-Leste',
								'TG' => 'Togo',
								'TK' => 'Tokelau',
								'TO' => 'Tonga',
								'TT' => 'Trinidad & Tobago',
								'TA' => 'Tristan da Cunha',
								'TN' => 'Tunisia',
								'TR' => 'Turkey',
								'TM' => 'Turkmenistan',
								'TC' => 'Turks & Caicos Islands',
								'TV' => 'Tuvalu',
								'UM' => 'U.S. Outlying Islands',
								'VI' => 'U.S. Virgin Islands',
								'UG' => 'Uganda',
								'UA' => 'Ukraine',
								'AE' => 'United Arab Emirates',
								'GB' => 'United Kingdom',
								'US' => 'United States',
								'UY' => 'Uruguay',
								'UZ' => 'Uzbekistan',
								'VU' => 'Vanuatu',
								'VA' => 'Vatican City',
								'VE' => 'Venezuela',
								'VN' => 'Vietnam',
								'WF' => 'Wallis & Futuna',
								'EH' => 'Western Sahara',
								'YE' => 'Yemen',
								'ZM' => 'Zambia',
								'ZW' => 'Zimbabwe',
							],
							'default_value'     => false,
							'return_format'     => 'value',
							'multiple'          => 1,
							'allow_null'        => 0,
							'ui'                => 1,
							'ajax'              => 0,
							'placeholder'       => '',
							'parent_repeater'   => 'field_6661bc6962d3d',
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'office',
					],
				],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'field',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		],
	);
endif;
