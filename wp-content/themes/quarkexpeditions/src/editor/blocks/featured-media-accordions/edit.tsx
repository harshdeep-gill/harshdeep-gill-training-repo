/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
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
	// Get child blocks attributes.
	const accordionMediaImage = useSelect(

		// Get the image attribute from the child block.
		( select: any ) => {
			// Get the child blocks.
			const childBlocks = select( blockEditorStore ).getBlocks( clientId );

			// Find the child block that is selected.
			let childBlock = childBlocks.find( ( block: any ) => {
				// Check if the block is selected or has an inner block selected.
				return select( blockEditorStore ).isBlockSelected( block.clientId ) || select( blockEditorStore ).hasSelectedInnerBlock( block.clientId, true );
			} );

			// If no child block is selected, select the first child block image.
			if ( ! childBlock && childBlocks.length > 0 ) {
				childBlock = childBlocks[ 0 ];
			}

			// Return the image attribute if the child block has an image.
			return childBlock && 0 < Object.keys( childBlock.attributes.image ).length ? childBlock.attributes.image : null;
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
			renderAppender: InnerBlocks.ButtonBlockAppender,

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
