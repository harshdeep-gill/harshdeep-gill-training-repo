/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	Placeholder,
	PanelBody,
} from '@wordpress/components';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const {
	ImageControl,
	LinkControl,
} = gumponents.components;

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import Section from '../../components/section';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block Attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set Block Props
	const blockProps = useBlockProps( {
		className: classnames( className, 'quark-search-filters-bar' ),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Search Filters Bar Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Antarctic CTA Image', 'qrk' ) }
						value={ attributes.antarcticImage ? attributes.antarcticImage.id : null }
						size="large"
						help={ __( 'Choose an image', 'qrk' ) }
						onChange={ ( antarcticImage: object ) => setAttributes( { antarcticImage } ) }
					/>
					<LinkControl
						label={ __( 'Antarctic CTA URL', 'qrk' ) }
						value={ attributes.antarcticCtaUrl }
						help={ __( 'Enter an URL for Antarctic CTA', 'qrk' ) }
						onChange={ ( antarcticCtaUrl: object ) => setAttributes( { antarcticCtaUrl } ) }
					/>
					<ImageControl
						label={ __( 'Arctic CTA Image', 'qrk' ) }
						value={ attributes.arcticImage ? attributes.arcticImage.id : null }
						size="large"
						help={ __( 'Choose an image', 'qrk' ) }
						onChange={ ( arcticImage: object ) => setAttributes( { arcticImage } ) }
					/>
					<LinkControl
						label={ __( 'Arctic CTA URL', 'qrk' ) }
						value={ attributes.arcticCtaUrl }
						help={ __( 'Enter an URL for Arctic CTA', 'qrk' ) }
						onChange={ ( arcticCtaUrl: object ) => setAttributes( { arcticCtaUrl } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				<Placeholder icon="layout" label={ __( 'Search Filters Bar', 'qrk' ) }>
					<p>{ __( 'This block will be rendered on the front-end.', 'qrk' ) }</p>
				</Placeholder>
			</Section>
		</>
	);
}
