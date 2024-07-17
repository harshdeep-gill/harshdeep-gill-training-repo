/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
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
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'season-highlights__highlight' ),
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
		selectedIcon = <Icon className="season-highlights__cross-icon" icon="no" />;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Season Highlight Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Icon', 'qrk' ) }
						help={ __( 'Select the icon for this highlight', 'qrk' ) }
						value={ attributes.icon }
						options={ [
							{ label: __( 'Select Icon…', 'qrk' ), value: '' },
							{ label: __( 'Penguin Courting', 'qrk' ), value: 'court' },
							{ label: __( 'Penguin Hatching', 'qrk' ), value: 'hatch' },
							{ label: __( 'Penguin Nesting', 'qrk' ), value: 'nest' },
							{ label: __( 'Penguin Chicks', 'qrk' ), value: 'penguin-chicks' },
							{ label: __( 'Seabird', 'qrk' ), value: 'seabird' },
							{ label: __( 'Whale', 'qrk' ), value: 'whale' },
							{ label: __( 'Seal', 'qrk' ), value: 'seal' },
							{ label: __( 'Elephant Seal', 'qrk' ), value: 'elephant-seal' },
							{ label: __( 'Glacier', 'qrk' ), value: 'glacier' },
						] }
						onChange={ ( icon: string ) => setAttributes( { icon } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<span className="season-highlights__icon">
					{ selectedIcon }
				</span>
				<RichText
					tagName="span"
					className="season-highlights__highlight-title"
					placeholder={ __( 'Highlight Name…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
