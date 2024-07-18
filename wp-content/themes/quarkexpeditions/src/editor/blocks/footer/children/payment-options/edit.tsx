/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../../../icons';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
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
}
