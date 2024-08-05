/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object} props Component properties.
 */
export default function Edit( {}: BlockEditAttributes ) {
	// Get block props.
	const blockProps = useBlockProps( {
		className: classnames( 'secondary-navigation__buttons' ),
	} );

	// Get inner blocks props.
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ 'quark/buttons' ],
			template: [
				[ 'quark/buttons', {}, [ [ 'quark/button', { isSizeBig: true } ] ] ],
			],
		}
	);

	// Return markup.
	return (
		<div { ...innerBlockProps }></div>
	);
}
