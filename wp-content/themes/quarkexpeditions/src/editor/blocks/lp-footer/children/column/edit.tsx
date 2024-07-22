/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

/**
 * Child blocks.
 */
import * as socialLinks from '../social-links';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( { className: 'lp-footer__column' } );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {}, {
		allowedBlocks: [ 'core/paragraph', 'core/list', socialLinks.name, 'core/heading', 'quark/logo-grid' ],
		template: [
			[ 'core/paragraph' ],
		],
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Column Options', 'qrk' ) }>
					<TextControl
						label="Enter URL for the block"
						value={ attributes.url }
						onChange={ ( url ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } >
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
