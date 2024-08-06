/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	useSelect,
	useDispatch,
} from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/editor';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { Img } = gumponents.components;

/**
 * Styles.
 */
import './editor.scss';

/**
 * Children blocks
 */
import * as item from './children/item';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 * @param {string} props.clientId  Client ID.
 */
export default function Edit( { className, clientId }: BlockEditAttributes ): JSX.Element {
	// Get the remove block function.
	const { removeBlock } = useDispatch( blockEditorStore );

	// Get child blocks attributes.
	const accordionMediaImage = useSelect(

		// Get the image attribute from the child block.
		( select: any ) => {
			// Get the child blocks.
			const childBlocks = select( blockEditorStore ).getBlocks( clientId );

			// Find the child block that is selected.
			const childBlock = childBlocks.find( ( block: any ) => {
				// Check if the block is selected or has an inner block selected.
				return select( blockEditorStore ).isBlockSelected( block.clientId ) || select( blockEditorStore ).hasSelectedInnerBlock( block.clientId, true );
			} );

			// Return the image attribute if the child block has an image.
			return childBlock && 0 < Object.keys( childBlock.attributes.image ).length ? childBlock.attributes.image : null;
		},
		[],
	);

	// Check if the appender should be shown and remove the last child block if there are more than 5 child blocks.
	const showBlockAppender = useSelect(
		( select: any ) => {
			// Get the child blocks.
			const childBlocks = select( blockEditorStore ).getBlocks( clientId );

			// Check if the number of child blocks is greater than 5.
			if ( 5 < childBlocks.length ) {
				// Remove the last child block.
				removeBlock( childBlocks[ childBlocks.length - 1 ].clientId );
			}

			// Return whether the appender should be shown.
			return 5 > childBlocks.length;
		},
		[],
	);

	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'featured-media-accordions',
		),
	} );

	// Set inner blocks props.
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'accordion' },
		{
			allowedBlocks: [ item.name ],
			template: [ [ item.name ], [ item.name ], [ item.name ] ],
			renderAppender: showBlockAppender ? InnerBlocks.ButtonBlockAppender : undefined,

			// @ts-ignore
			orientation: 'vertical',
		}
	);

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<Section>
				<div className="two-columns grid">
					<div className="two-columns__column">
						<figure className="featured-media-accordions__image">
							{
								accordionMediaImage && (
									<Img
										value={ accordionMediaImage }
										alt={ __( 'Accordion Image', 'qrk' ) }
									/>
								)
							}
						</figure>
					</div>
					<div className="two-columns__column">
						<div { ...innerBlockProps } />
					</div>
				</div>
			</Section>
		</div>
	);
}
