/**
 * WordPress dependencies.
 */
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/expedition-details';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Add classnames.
	const blockProps = useBlockProps( { className } );

	// Return the markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Secondary Navigation Item Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Enter URL', 'qrk' ) }
						value={ attributes.departuresUrl }
						help={ __( 'Enter a URL for this navigation item', 'qrk' ) }
						onChange={ ( departuresUrl: object ) => setAttributes( { departuresUrl } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block={ name }
				/>
			</div>
		</>
	);
}
