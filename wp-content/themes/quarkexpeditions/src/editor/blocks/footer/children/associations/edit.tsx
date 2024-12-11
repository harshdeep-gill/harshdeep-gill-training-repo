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
	const blockProps = useBlockProps( { className: classnames( className, 'footer__associations' ) } );

	// Return the block's markup.
	return (
		<ul { ...blockProps } >
			<li className="footer__association">
				{ icons.association.iaato }
			</li>
			<li className="footer__association">
				{ icons.association.aeco }
			</li>
		</ul>
	);
}
