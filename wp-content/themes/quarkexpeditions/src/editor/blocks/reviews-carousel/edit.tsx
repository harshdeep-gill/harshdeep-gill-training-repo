/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './children/reviews-carousel-item';

/**
 * Edit Block.
 *
 * @param {string} className The block class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'reviews-carousel' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'reviews-carousel__slider' ),
	}, {
		allowedBlocks: [ item.name ],
		template: [ [ item.name ] ],
		renderAppender: InnerBlocks.ButtonBlockAppender,
	} );

	// Return the block's markup.
	return (
		<>
			<Section { ...blockProps }>
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
