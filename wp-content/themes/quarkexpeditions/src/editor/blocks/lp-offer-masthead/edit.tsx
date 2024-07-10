/**
 * WordPress dependencies.
 */
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

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
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img, SelectImage } = gumponents.components;

/**
 * Child blocks.
 */
import * as offerImage from './children/offer-image';
import * as caption from './children/caption';
import * as content from './children/content';

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
		className: classnames( className, 'lp-offer-masthead' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {},
		{
			allowedBlocks: [ offerImage.name, caption.name, content.name ],
			template: [ [ offerImage.name ], [ caption.name ], [ content.name ] ],
		}
	);

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'LP Offer Masthead Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Background Image', 'qrk' ) }
						value={ attributes.bgImage ? attributes.bgImage.id : null }
						size="large"
						help={ __( 'Choose a background image.', 'qrk' ) }
						onChange={ ( bgImage: object ) => setAttributes( { bgImage } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }
				fullWidth={ true }
				seamless={ true }
				background={ true }
				backgroundColor={ 'black' }
			>
				<figure className="lp-offer-masthead__image-wrap">
					{
						attributes.bgImage &&
						<Img
							value={ attributes.bgImage }
							className="lp-offer-masthead__image"
						/>
					}
				</figure>
				<div className="lp-offer-masthead__content wrap">
					<figure className="lp-offer-masthead__logo">
						<SelectImage
							image={ attributes.logoImage }
							placeholder="Choose a Logo Image"
							size="medium"
							onChange={ ( logoImage: Object ): void => {
								// Set image.
								setAttributes( { logoImage: null } );
								setAttributes( { logoImage } );
							} }
						/>
					</figure>
					<div { ...innerBlockProps } />
				</div>
			</Section>
		</>
	);
}
