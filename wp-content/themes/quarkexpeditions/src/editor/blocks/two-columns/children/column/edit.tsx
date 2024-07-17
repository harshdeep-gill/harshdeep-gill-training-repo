/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'two-columns__column' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlocksProps = useInnerBlocksProps( { ...blocksProps }, {
		template: [ [ 'core/paragraph', { placeholder: __( 'Write content…', 'qrk' ) } ] ],
		templateLock: false,
	} );

	// Return the block's markup.
	return (
		<div { ...innerBlocksProps } />
	);
}
