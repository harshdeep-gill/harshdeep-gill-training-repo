/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../icons';
import { Icon, PanelBody, SelectControl } from '@wordpress/components';

/**
 * Block name.
 */
export const name: string = 'quark/footer-icon';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Icon', 'qrk' ),
	description: __( 'Display the footer icon block.', 'qrk' ),
	parent: [ 'quark/footer-column' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'icon', 'qrk' ),
	],
	attributes: {
		type: {
			type: 'string',
			default: '',
			enum: [ 'call', 'brochure', 'email', '' ],
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'footer__icon' ),
		} );

		// Prepare icon.
		let selectedIcon: any = '';

		// Set icon.
		if ( attributes.type && '' !== attributes.type ) {
			// Setting icon.
			selectedIcon = icons[ attributes.type ] ?? '';
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
							help={ __( 'Select icon.', 'qrk' ) }
							value={ attributes.icon }
							options={ [
								{ label: __( 'Select Icon…', 'qrk' ), value: '' },
								{ label: __( 'Call', 'qrk' ), value: 'call' },
								{ label: __( 'Brochure', 'qrk' ), value: 'brochure' },
								{ label: __( 'Mail', 'qrk' ), value: 'mail' },
							] }
							onChange={ ( type: string ) => setAttributes( { type } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<span { ...blockProps }>
					{ selectedIcon }
				</span>
			</>
		);
	},
	save() {
		// Return null;
		return null;
	},
};
