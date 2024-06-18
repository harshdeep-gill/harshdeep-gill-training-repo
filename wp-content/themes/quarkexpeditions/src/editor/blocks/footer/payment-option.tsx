/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { Icon, PanelBody, SelectControl } from '@wordpress/components';

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
export const name: string = 'quark/footer-payment-option';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Payment Option', 'qrk' ),
	description: __( 'Display a single payment option.', 'qrk' ),
	parent: [ 'quark/footer-payment-options' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'payment', 'qrk' ),
		__( 'option', 'qrk' ),
	],
	attributes: {
		type: {
			type: 'string',
			default: '',
			enum: [ 'amex', 'discover', 'mastercard', 'visa', '' ],
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
			className: classnames( className, 'footer__payment-option' ),
		} );

		// Prepare icon.
		let selectedIcon: any = '';

		// Set icon.
		if ( attributes.type && '' !== attributes.type ) {
			// Setting icon.
			selectedIcon = icons.payment[ attributes.type ] ?? '';
		}

		// Fallback icon.
		if ( ! selectedIcon || '' === selectedIcon ) {
			selectedIcon = <Icon icon="no" />;
		}

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Payment Option Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Payment Option', 'qrk' ) }
							help={ __( 'Select the payment option.', 'qrk' ) }
							value={ attributes.type }
							options={ [
								{ label: __( 'Select Icon…', 'qrk' ), value: '' },
								{ label: __( 'Visa', 'qrk' ), value: 'visa' },
								{ label: __( 'Mastercard', 'qrk' ), value: 'mastercard' },
								{ label: __( 'Amex', 'qrk' ), value: 'amex' },
								{ label: __( 'Discover', 'qrk' ), value: 'discover' },
							] }
							onChange={ ( type: string ) => setAttributes( { type } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<li { ...blockProps }>
					{ selectedIcon }
				</li>
			</>
		);
	},
	save() {
		// Return null;
		return null;
	},
};
