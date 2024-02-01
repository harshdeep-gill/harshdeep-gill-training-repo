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
	TextareaControl,
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
export const name: string = 'quark/icon-info-columns-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Icon Info Columns - Column', 'qrk' ),
	description: __( 'Individual column in the icon info columns.', 'qrk' ),
	parent: [ 'quark/icon-info-columns' ],
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
		info: {
			type: 'string',
			default: '',
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
			className: classnames( className, 'icon-info-columns__column' ),
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
								{ label: __( 'Star', 'qrk' ), value: 'star' },
								{ label: __( 'Compass', 'qrk' ), value: 'compass' },
								{ label: __( 'Itinerary', 'qrk' ), value: 'itinerary' },
								{ label: __( 'Mountains', 'qrk' ), value: 'mountains' },
								{ label: __( 'Ship', 'qrk' ), value: 'ship' },
							] }
							onChange={ ( icon: string ) => setAttributes( { icon } ) }
						/>
						<TextareaControl
							label={ __( 'Info', 'qrk' ) }
							help={ __( 'Enter the info that appears on rollover.', 'qrk' ) }
							value={ attributes.info }
							onChange={ ( info: string ) => setAttributes( { info } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blocksProps } tabIndex={1}>
					<div className="icon-info-columns__icon">
						{ selectedIcon }
					</div>
					<RichText
						tagName="p"
						className="icon-info-columns__title"
						placeholder={ __( 'Write title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
					{ '' !== attributes.info &&
						<div className="icon-info-columns__info">
							<span className="icon-info-columns__info-icon">
								{ icons.info }
							</span>
						</div>
					}
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
