/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import { PanelBody } from '@wordpress/components';
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Children blocks
 */
import * as footerColumnTitle from './column-title';
import * as footerIcon from './icon';
import * as footerSocialLinks from './social-links';
import * as footerPaymentOptions from './payment-options';
import * as quarkButton from '../button';
import * as footerSiteLogo from './logo';

/**
 * Register children blocks.
 */
registerBlockType( footerColumnTitle.name, footerColumnTitle.settings );
registerBlockType( footerIcon.name, footerIcon.settings );
registerBlockType( footerSocialLinks.name, footerSocialLinks.settings );
registerBlockType( footerPaymentOptions.name, footerPaymentOptions.settings );
registerBlockType( footerSiteLogo.name, footerSiteLogo.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Column', 'qrk' ),
	description: __( 'Display a Column of the footer.', 'qrk' ),
	parent: [ 'quark/footer-top', 'quark/footer-middle' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'column', 'qrk' ),
	],
	attributes: {
		url: {
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
			className: classnames( className, 'footer__column' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [
					footerColumnTitle.name,
					footerIcon.name,
					footerSocialLinks.name,
					footerPaymentOptions.name,
					quarkButton.name,
					'core/paragraph',
					'core/list',
					'core/heading',
				],
				template: [ [ 'core/paragraph' ] ],
			}
		);

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Footer column options', 'qrk' ) }>
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this column', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...innerBlockProps } />
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
