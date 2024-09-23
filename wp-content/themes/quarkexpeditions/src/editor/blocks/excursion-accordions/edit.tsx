/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	Placeholder,
} from '@wordpress/components';
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
	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Excursion Accordion Options', 'qrk' ) }>
					<TaxonomyRelationshipControl
						label={ __( 'Select Destinations.', 'qrk' ) }
						help={ __( 'Select one ore more Destination terms. They will be categorized automatically.', 'qrk' ) }
						taxonomies="qrk_excursion"
						value={ attributes.destinationTermIds }
						onSelect={ ( termIDs: Array<{ term_id: number }> ) => setAttributes( { destinationTermIds: termIDs.map( ( term ) => term.term_id ) } ) }
						buttonLabel={ __( 'Select Destinations', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section className={ classnames( className ) }>
				{
					attributes.destinationTermIds.length > 0 ? (
						<ServerSideRender
							block={ name }
							attributes={ attributes }
						/>
					) : (
						<Placeholder icon="layout" label={ __( 'Excursion Accordion', 'qrk' ) }>
							<p>{ __( 'Select one or more destination terms.', 'qrk' ) }</p>
						</Placeholder>
					)
				}
			</Section>
		</>
	);
}
