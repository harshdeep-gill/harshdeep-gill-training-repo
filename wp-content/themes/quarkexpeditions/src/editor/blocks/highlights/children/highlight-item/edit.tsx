/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	Icon,
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../../../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as highlightTitle from '../title';
import * as highlightOverline from '../overline';
import * as highlightText from '../text';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// Prepare block props.
	const blocksProps = useBlockProps( {
		className: classnames( className, 'highlights__item' ),
	} );

	// Prepare InnerBlocks props.
	const innerBlockProps = useInnerBlocksProps( {
		className: 'highlights__content',
	}, {
		allowedBlocks: [ highlightTitle.name, highlightOverline.name, highlightText.name ],
		template: [
			[ highlightTitle.name ],
			[ highlightOverline.name ],
			[ highlightText.name ],
		],
	} );

	// Prepare icon.
	let selectedIcon: any = '';

	// Set icon.
	if ( attributes.icon && '' !== attributes.icon ) {
		// Kebab-case to camel-case.
		const iconName: string = attributes.icon.replace( /-([a-z])/g, ( _: any, group:string ) => group.toUpperCase() );
		selectedIcon = icons[ iconName ] ?? '';
	}

	// Fallback icon.
	if ( ! selectedIcon || '' === selectedIcon ) {
		selectedIcon = <Icon icon="no" />;
	}

	// Icon class.
	const iconClasses = classnames( 'highlights__icon', {
		'highlights__icon--border': attributes.border,
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Item Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Icon', 'qrk' ) }
						help={ __( 'Select the icon.', 'qrk' ) }
						value={ attributes.icon }
						options={ [
							{ label: __( 'Select Icon…', 'qrk' ), value: '' },
							{ label: __( 'Compass', 'qrk' ), value: 'compass2' },
							{ label: __( 'Zodiac Cruising', 'qrk' ), value: 'zodiac-cruising' },
							{ label: __( 'Whale Tail', 'qrk' ), value: 'whale-tail' },
							{ label: __( 'House', 'qrk' ), value: 'house' },
							{ label: __( 'Iceberg', 'qrk' ), value: 'iceberg' },
							{ label: __( 'Time', 'qrk' ), value: 'time2' },
							{ label: __( 'Brochure', 'qrk' ), value: 'brochure' },
							{ label: __( 'Dollar Sign', 'qrk' ), value: 'dollar-sign' },
							{ label: __( 'Explore', 'qrk' ), value: 'explore' },
							{ label: __( 'Ship', 'qrk' ), value: 'ship' },
							{ label: __( 'Presentations', 'qrk' ), value: 'presentations' },
							{ label: __( 'Wildlife Penguin', 'qrk' ), value: 'wildlife-penguin' },
							{ label: __( 'Paddling Excursions', 'qrk' ), value: 'paddling-excursions' },
							{ label: __( 'Flight', 'qrk' ), value: 'flight' },
							{ label: __( 'Helicopter', 'qrk' ), value: 'helicopter' },
							{ label: __( 'Bird', 'qrk' ), value: 'bird' },
							{ label: __( 'Fly The Drake', 'qrk' ), value: 'fly-the-drake' },
							{ label: __( 'Landscapes', 'qrk' ), value: 'landscapes' },
							{ label: __( 'Drink', 'qrk' ), value: 'drink' },
							{ label: __( 'Footsteps', 'qrk' ), value: 'footsteps' },
							{ label: __( 'Sea Kayaking', 'qrk' ), value: 'sea-kayaking' },
							{ label: __( 'Relaxed Traveling', 'qrk' ), value: 'relaxed-traveling' },
							{ label: __( 'Wildlife Polar Bear', 'qrk' ), value: 'wildlife-polar-bear' },
							{ label: __( 'Hiking', 'qrk' ), value: 'hiking' },
							{ label: __( 'Camera', 'qrk' ), value: 'camera' },
							{ label: __( 'Sun', 'qrk' ), value: 'sun' },
							{ label: __( 'Checklister', 'qrk' ), value: 'checklister' },
							{ label: __( 'Hot Air Balloon', 'qrk' ), value: 'hot-air-balloon' },
							{ label: __( 'Binoculars', 'qrk' ), value: 'binoculars' },
							{ label: __( 'Mountain Biking', 'qrk' ), value: 'mountain-biking' },
							{ label: __( 'Viking Ship', 'qrk' ), value: 'viking-ship' },
							{ label: __( 'Pin', 'qrk' ), value: 'pin' },
							{ label: __( 'OX Icon', 'qrk' ), value: 'ox-icon' },
							{ label: __( 'Fjord', 'qrk' ), value: 'fjord' },
							{ label: __( 'Eclipse', 'qrk' ), value: 'eclipse' },
						] }
						onChange={ ( icon: string ) => setAttributes( { icon } ) }
					/>
					<ToggleControl
						label={ __( 'Icon border?', 'qrk' ) }
						help={ __( 'Add a border to the Icon.', 'qrk' ) }
						checked={ attributes.border }
						onChange={ ( border: boolean ) => setAttributes( { border } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				<div className={ iconClasses }>
					{ selectedIcon }
				</div>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
