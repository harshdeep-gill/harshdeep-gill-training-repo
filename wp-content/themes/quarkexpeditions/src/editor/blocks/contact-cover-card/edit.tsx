/**
 * WordPress dependencies
 */
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal components.
 */
import Section from '../../components/section';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External components.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img } = gumponents.components;

/**
 * Child blocks.
 */
import * as title from './children/title';
import * as description from './children/description';
import * as contactInfo from './children/contact-info';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'contact-cover-card' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'contact-cover-card__content' ),
	},
	{
		allowedBlocks: [ title.name, description.name, contactInfo.name ],
		template: [ [ title.name ], [ description.name ], [ contactInfo.name ] ],
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Contact Cover Card Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="large"
						help={ __( 'Choose a background image.', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps } >
				<figure className="contact-cover-card__image-wrap">
					<Img value={ attributes.image } className="contact-cover-card__image" />
				</figure>
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
