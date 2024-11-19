/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, Placeholder, TextControl } from '@wordpress/components';

/**
 * Styles.
 */
import './editor.scss';
import Section from '../../components/section';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set block properties.
	const blockProps = useBlockProps();

	// Sanitize the URL. Remove - query string, - trailing slash, extract last part of the URL.
	const getInstagramPostId = ( url: string ): string => {
		// Remove query string.
		url = url.split( '?' )[ 0 ];

		// Remove trailing slash.
		url = url.replace( /\/$/, '' );

		// Extract last part of the URL.
		const parts = url.split( '/' );

		// Return the last part of the URL.
		return parts[ parts.length - 1 ];
	};

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Instagram Post URL', 'qrk' ) }>
					<TextControl
						label={ __( 'Instagram Post URL', 'instagram-embed-block' ) }
						value={ attributes.url }
						onChange={ ( value ) => setAttributes( { url: value, instagramPostId: getInstagramPostId( value ) } ) }
						placeholder={ __( 'Enter Instagram URL…', 'instagram-embed-block' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				{ attributes.instagramPostId ? (
					<iframe
						title={ __( 'Instagram Embed', 'qrk' ) }
						className="instagram-embed__media"
						src={ `https://www.instagram.com/p/${ attributes.instagramPostId }/embed` }
					/>
				) : (
					<Placeholder
						icon="instagram"
						label={ __( 'Instagram Embed', 'qrk' ) }
						instructions={ __( 'Enter the Instagram post URL.', 'qrk' ) }
					>
					</Placeholder>
				) }
			</Section>
		</>
	);
}
