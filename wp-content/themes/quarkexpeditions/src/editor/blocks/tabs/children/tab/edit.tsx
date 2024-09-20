/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import * as BlockEditorSelectors from '@wordpress/block-editor/store/selectors';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit component.
 *
 * @param {Object}  props            Component props.
 * @param {string}  props.className  Block class name.
 * @param {boolean} props.isSelected Block is selected.
 * @param {string}  props.clientId   Block client ID.
 * @param {Object}  props.context    Block context.
 */
export default function Edit( {
	className,
	isSelected,
	clientId,
	context,
}: BlockEditAttributes ) {
	// Has selected inner block.
	const hasSelectedInnerBlock = useSelect(
		( select: any ) =>
			( select( blockEditorStore ) as typeof BlockEditorSelectors ).hasSelectedInnerBlock(
				clientId,
			),
		[ clientId ],
	);

	// Block props.
	const blocksProps = useBlockProps( {
		className: classnames( className, 'tabs__tab', {
			'tabs__tab--active': isSelected || hasSelectedInnerBlock || clientId === context[ 'quark/tabs' ],
		} ),
	} );

	// Inner blocks props.
	const innerBlockProps = useInnerBlocksProps(
		{ ...blocksProps },
		{
			template: [ [ 'core/paragraph', { placeholder: __( 'Add content…', 'qrk' ) } ] ],
		},
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
