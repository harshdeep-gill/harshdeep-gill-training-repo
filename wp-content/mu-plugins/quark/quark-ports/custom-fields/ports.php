<?php
/**
 * Custom fields: Port posts.
 *
 * @package quark-ports
 */

// Check if ACF function exists or not.
if ( function_exists( 'acf_add_local_field_group' ) ) :

	// Add local filed group for Port CPT.
	acf_add_local_field_group(
		[
			'key'                   => 'group_65ee9afeaad47',
			'title'                 => 'Ports',
			'fields'                => [
				[
					'key'               => 'field_65ee9b8f2cfc7',
					'label'             => 'Port Code',
					'name'              => 'port_code',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'The 6-character (including space) port code as defined by UNECE. Example: AR USH',
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
					'key'               => 'field_65ee9ce12cfcb',
					'label'             => 'Country',
					'name'              => 'country',
					'aria-label'        => '',
					'type'              => 'select',
					'instructions'      => '',
					'required'          => 1,
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
					'multiple'          => 0,
					'allow_null'        => 0,
					'ui'                => 0,
					'ajax'              => 0,
					'placeholder'       => '',
				],
				[
					'key'               => 'field_65ee9d0c2cfcc',
					'label'             => 'Locality / Suburb / City',
					'name'              => 'locality',
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
					'key'               => 'field_65eea02d2cfcd',
					'label'             => 'Administrative area / State / Province',
					'name'              => 'administrative_area',
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
					'key'               => 'field_65ee9c7e2cfc8',
					'label'             => 'Latitude',
					'name'              => 'latitude',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Enter either in decimal -34.61161 or sexagesimal format -34° 36\' 41.796"',
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
					'key'               => 'field_65ee9c9e2cfc9',
					'label'             => 'Longitude',
					'name'              => 'longitude',
					'aria-label'        => '',
					'type'              => 'text',
					'instructions'      => 'Enter either in decimal -58.367685 or sexagesimal format -58° 22\' 3.666"',
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
			],
			'location'              => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'qrk_port',
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
