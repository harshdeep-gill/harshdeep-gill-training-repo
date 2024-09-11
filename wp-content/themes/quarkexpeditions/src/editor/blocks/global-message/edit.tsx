/**
 * Wordpress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Edit Component.
 *
 * @parma {string} props.className Class name.
 */

/**
 * External dependencies.
 */
import classnames from 'classnames';

export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'global-message' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blocksProps },
		{
			allowedBlocks: [ 'core/paragraph' ],
			template: [ [ 'core/paragraph', { placeholder: __( 'Write global message...', 'qrk' ) } ] ],
			templateLock: 'insert',
		},
	);

	// Return tge block's markup.
	return(
	 <div { ...innerBlockProps } />
	);
}
