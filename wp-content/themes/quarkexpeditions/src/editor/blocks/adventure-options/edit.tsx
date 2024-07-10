/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { PanelBody } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { TaxonomyRelationshipControl } = gumponents.components;

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import metadata from './block.json';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Return the markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Adventure Options Options', 'qrk' ) }>
					<TaxonomyRelationshipControl
						label={ __( 'Select Adventure Option Category.', 'et' ) }
						help={ __( 'Select Adventure Option Category', 'et' ) }
						taxonomies="qrk_adventure_option_category"
						value={ attributes.termIDs }
						onSelect={ ( terms: Array<{ term_id: number }> ) => setAttributes( { termIDs: terms.map( ( term ) => term.term_id ) } ) }
						buttonLabel={ __( 'Select Adventure Option Category', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section className={ classnames( className ) }>
				<ServerSideRender
					block={ name }
					attributes={ attributes }
				/>
			</Section>
		</>
	);
}
