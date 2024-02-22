/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
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
export const name: string = 'quark/lp-footer-icon';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'LP Footer icon', 'qrk' ),
	description: __( 'Individual icon in the LP footer columns.', 'qrk' ),
	parent: [ 'quark/lp-footer-column' ],
	icon: 'info',
	category: 'layout',
	keywords: [ __( 'icon', 'qrk' ) ],
	attributes: {
		icon: {
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
			className: classnames( className, 'lp-footer__icon' ),
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
					<PanelBody title={ __( 'Icon Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Icon', 'qrk' ) }
							help={ __( 'Select the icon.', 'qrk' ) }
							value={ attributes.icon }
							options={ [
								{ label: __( 'Select Icon…', 'qrk' ), value: '' },
								{ label: __( 'Call', 'qrk' ), value: 'call' },
								{ label: __( 'Brochure', 'qrk' ), value: 'brochure' },
								{ label: __( 'Mail', 'qrk' ), value: 'mail' },
							] }
							onChange={ ( icon: string ) => setAttributes( { icon } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<span { ...blocksProps }>
					{ selectedIcon }
				</span>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
