/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * Block name.
 */
export const name: string = 'quark/footer-payment-options';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Payment options', 'qrk' ),
	description: __( 'Display the payment options.', 'qrk' ),
	parent: [ 'quark/footer-column' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'payment', 'qrk' ),
		__( 'options', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: classnames( className, 'footer__payment-options' ) } );

		// Return the block's markup.
		return (
			<ul { ...blockProps } >
				<li className="footer__payment-option">
					{ icons.payment.visa }
				</li>
				<li className="footer__payment-option">
					{ icons.payment.mastercard }
				</li>
				<li className="footer__payment-option">
					{ icons.payment.amex }
				</li>
				<li className="footer__payment-option">
					{ icons.payment.discover }
				</li>
			</ul>
		);
	},
	save() {
		// No markup to save.
		return null;
	},
};
