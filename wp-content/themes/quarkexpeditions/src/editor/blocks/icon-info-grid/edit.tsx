/**
 * WordPress dependencies.
 */
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import './../../../front-end/components/icon-info-grid/style.scss';
import './editor.scss';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Prepare block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'icon-info-grid' ),
	} );

	// Prepare inner block props.
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ 'quark/icon-info-grid-item' ],
		template: [ [ 'quark/icon-info-grid-item' ], [ 'quark/icon-info-grid-item' ], [ 'quark/icon-info-grid-item' ] ],
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps } >
			<div { ...innerBlockProps } />
		</div>
	);
}
