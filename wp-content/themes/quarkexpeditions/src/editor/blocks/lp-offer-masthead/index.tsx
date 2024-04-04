/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	PanelBody,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/lp-offer-masthead/style.scss';
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
import * as offerImage from './offer-image';
import * as caption from './caption';
import * as content from './content';

/**
 * Register child block.
 */
registerBlockType( offerImage.name, offerImage.settings );
registerBlockType( caption.name, caption.settings );
registerBlockType( content.name, content.settings );

/**
 * Block name.
 */
export const name: string = 'quark/lp-offer-masthead';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Landing Page Offer Masthead', 'qrk' ),
	description: __( 'Display a masthead with offers.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'masthead', 'qrk' ),
		__( 'offers', 'qrk' ),
	],
	attributes: {
		bgImage: {
			type: 'object',
		},
		logoImage: {
			type: 'object',
			default: null,
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'lp-offer-masthead' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {},
			{
				allowedBlocks: [ offerImage.name, caption.name, content.name ],
				template: [ [ offerImage.name ], [ caption.name ], [ content.name ] ],
				templateLock: 'insert',
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
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
