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
import * as expeditionDetails from '../../../expedition-details';

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
		className: classnames( className, 'two-columns__column' ),
	} );

	// Set inner block props - Only Allow Expedition Details block.
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [
				expeditionDetails.name,
			],
			template: [
				[ expeditionDetails.name ],
			],
		}
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
