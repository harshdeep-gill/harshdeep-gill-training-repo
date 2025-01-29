/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img, LinkControl } = gumponents.components;

/**
 * Child blocks.
 */
import * as title from '../title';
import * as description from '../description';
import * as tag from '../tag';
import * as cta from '../cta';
import * as overline from '../overline';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {Object}   props.className     Block class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set block attributes.
	const blocksProps = useBlockProps( {
		className: classnames(
			className,
			'info-cards__card-content',
			'info-cards__card-content--' + attributes.contentPosition,
		),
	} );

	// Set inner blocks attributes.
	const innerBlockProps = useInnerBlocksProps( { ...blocksProps },
		{
			allowedBlocks: [ title.name, description.name, tag.name, cta.name, overline.name ],
			template: [ [ tag.name ], [ overline.name ], [ title.name ], [ description.name ], [ cta.name ] ],
		} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Info Cards - Card Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Enter URL for the Card', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter a URL for this item', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
					<SelectControl
						label={ __( 'Content Position', 'qrk' ) }
						value={ attributes.contentPosition }
						options={ [
							{ label: __( 'Top', 'qrk' ), value: 'top' },
							{ label: __( 'Bottom', 'qrk' ), value: 'bottom' },
						] }
						onChange={ ( contentPosition: string ) => setAttributes( { contentPosition } ) }
					/>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="medium"
						help={ __( 'Choose a background image.', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div className="info-cards__card color-context--dark" >
				<div className="maybe-link">
					<figure className="info-cards__image-wrap">
						<Img value={ attributes.image } className="info-cards__image" />
					</figure>
					<div { ...innerBlockProps } />
				</div>
			</div>
		</>
	);
}
