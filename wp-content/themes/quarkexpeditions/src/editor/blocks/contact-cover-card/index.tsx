/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
} from '@wordpress/components';

/**
 * Internal components.
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
const { ImageControl, Img } = gumponents.components;

/**
 * Styles.
 */
import '../../../front-end/components/contact-cover-card/style.scss';
import './editor.scss';

/**
 * Child block.
 */
import * as contactInfo from './contact-info';
import * as title from './title';
import * as description from './description';

/**
 * Register child block.
 */
registerBlockType( contactInfo.name, contactInfo.settings );
registerBlockType( title.name, title.settings );
registerBlockType( description.name, description.settings );

/**
 * Block name.
 */
export const name: string = 'quark/contact-cover-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Contact Cover Card', 'qrk' ),
	description: __( 'Add a Contact Cover Card', 'qrk' ),
	category: 'layout',
	keywords: [ __( 'contact', 'qrk' ), __( 'card', 'qrk' ) ],
	attributes: {
		image: {
			type: 'object',
			default: {},
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
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
