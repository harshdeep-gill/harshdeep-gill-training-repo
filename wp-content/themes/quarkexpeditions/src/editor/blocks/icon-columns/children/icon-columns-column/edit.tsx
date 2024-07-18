/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	Icon,
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
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'icon-columns__column' ),
	} );

	// Prepare icon.
	let selectedIcon: any = '';

	// Set icon.
	if ( attributes.icon && '' !== attributes.icon ) {
		// Kebab-case to camel-case.
		let iconName: string = attributes.icon.replace( /-([a-z])/g, ( _: any, group:string ) => group.toUpperCase() );
		iconName = iconName.replace( 'duotone/', '' );
		selectedIcon = icons.duotone[ iconName ] ?? '';
	}

	// Fallback icon.
	if ( ! selectedIcon || '' === selectedIcon ) {
		selectedIcon = <Icon icon="no" />;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Column Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Icon', 'qrk' ) }
						help={ __( 'Select the icon.', 'qrk' ) }
						value={ attributes.icon }
						options={ [
							{ label: __( 'Select Icon…', 'qrk' ), value: '' },
							{ label: __( 'Hiker', 'qrk' ), value: 'duotone/hiker' },
							{ label: __( 'Person Check', 'qrk' ), value: 'duotone/person-check' },
							{ label: __( 'Person Compass', 'qrk' ), value: 'duotone/person-compass' },
							{ label: __( 'Small ship', 'qrk' ), value: 'duotone/small-ship' },
							{ label: __( 'Stars', 'qrk' ), value: 'duotone/stars' },
						] }
						onChange={ ( icon: string ) => setAttributes( { icon } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				<div className="icon-columns__icon">
					{ selectedIcon }
				</div>
				<RichText
					tagName="p"
					className="icon-columns__title"
					placeholder={ __( 'Write title…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
