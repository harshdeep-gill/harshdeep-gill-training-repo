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
							{ label: __( 'Flight Seeing', 'qrk' ), value: 'flightseeing' },
							{ label: __( 'Time', 'qrk' ), value: 'time2' },
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
