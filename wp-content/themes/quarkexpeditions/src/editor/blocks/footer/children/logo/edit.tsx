/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import classnames from 'classnames';
import icons from '../../../icons';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( { className: classnames( className, 'footer__logo' ) } );

	// Return the block's markup.
	return ( <span { ...blockProps }> { icons.logo } </span> );
}
