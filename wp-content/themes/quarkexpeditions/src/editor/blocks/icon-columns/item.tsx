/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
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
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/icon-columns-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Icon Columns - Column', 'qrk' ),
	description: __( 'Individual column in the icon columns.', 'qrk' ),
	parent: [ 'quark/icon-columns' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ) ],
	attributes: {
		icon: {
			type: 'string',
		},
		title: {
			type: 'string',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
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
			iconName = iconName.replace( 'duotone/', '' ).concat( 'Duotone' );
			selectedIcon = icons[ iconName ] ?? '';
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
	},
	save() {
		// Don't save anything.
		return null;
	},
};
