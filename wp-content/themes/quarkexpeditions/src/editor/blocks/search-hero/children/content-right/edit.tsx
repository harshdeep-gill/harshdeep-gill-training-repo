/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import * as heroDetailsCardSlider from '../../../hero-details-card-slider';

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
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'search-hero__right' ),
	} );

	// Set inner block props - Only Allow Hero Card Slider block.
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ heroDetailsCardSlider.name ],
			template: [ [ heroDetailsCardSlider.name, { showControls: false } ] ],
		}
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
