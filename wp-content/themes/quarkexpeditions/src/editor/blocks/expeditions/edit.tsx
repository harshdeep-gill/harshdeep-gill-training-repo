/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	Placeholder,
	SelectControl,
	RangeControl,
} from '@wordpress/components';
import {
	InspectorControls,
} from '@wordpress/block-editor';

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
const {
	PostRelationshipControl,
	TaxonomyRelationshipControl,
	MultiSelectControl,
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
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Initialize.
	let allOptionsSelected = false;

	// Check if all options are selected.
	if ( 'manual' === attributes.selection && 0 !== attributes.ids.length ) {
		allOptionsSelected = true;
	} else if ( 'byTerms' === attributes.selection && 0 !== attributes.termIds.length && 0 !== attributes.taxonomies.length ) {
		allOptionsSelected = true;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Expeditions Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Selection', 'qrk' ) }
						help={ __( 'Select how you would like to select expeditions', 'qrk' ) }
						value={ attributes.selection }
						options={ [
							{ label: __( 'Manual', 'qrk' ), value: 'manual' },
							{ label: __( 'By Terms', 'qrk' ), value: 'byTerms' },
						] }
						onChange={ ( selection: string ) => setAttributes( { selection } ) }
					/>
					{
						'manual' === attributes.selection &&
						<PostRelationshipControl
							label={ __( 'Select Expeditions', 'qrk' ) }
							help={ __( 'Select the expeditions to be displayed', 'qrk' ) }
							postTypes="qrk_expedition"
							value={ attributes.ids }
							onSelect={ ( expeditions: any ) => setAttributes( { ids: expeditions.map( ( expedition: any ) => expedition.ID ) } ) }
							button={ __( 'Select Expeditions', 'qrk' ) }
						/>
					}
					{ 'byTerms' === attributes.selection &&
						<MultiSelectControl
							label={ __( 'Taxonomies', 'qrk' ) }
							help={ __( 'Select one or more taxonomies', 'qrk' ) }
							options={ [
								{ label: __( 'Destinations', 'qrk' ), value: 'qrk_destination' },
								{ label: __( 'Expedition Categories', 'qrk' ), value: 'qrk_expedition_category' },
								{ label: __( 'Expedition Tags', 'qrk' ), value: 'qrk_expedition_tag' },
								{ label: __( 'Adventure Option Categories', 'qrk' ), value: 'qrk_adventure_option_category' },
							] }
							value={ attributes.taxonomies }
							onChange={ ( taxonomies: any ) => setAttributes( { taxonomies } ) }
						/>
					}
					{
						'byTerms' === attributes.selection &&
						attributes.taxonomies.length > 0 &&
						<TaxonomyRelationshipControl
							label={ __( 'Select Terms' ) }
							help={ __( 'Select the associated terms', 'qrk' ) }
							taxonomies={ attributes.taxonomies }
							value={ attributes.termIds }
							onSelect={ ( terms: any ) => setAttributes( { termIds: terms.map( ( term: any ) => term.term_id ) } ) }
						/>
					}
					{
						'byTerms' === attributes.selection &&
							<RangeControl
								label={ __( 'Total Expeditions', 'qrk' ) }
								help={ __( 'Select the total number of expeditions to be displayed', 'qrk' ) }
								value={ attributes.totalPosts }
								onChange={ ( totalPosts ) => setAttributes( { totalPosts } ) }
								min={ 1 }
								max={ 100 }
							/>
					}
				</PanelBody>
			</InspectorControls>
			<Section className={ classnames( className ) }>
				{
					allOptionsSelected
						? (
							<ServerSideRender
								block={ name }
								attributes={ attributes }
							/>
						) : (
							<Placeholder icon="layout" label={ __( 'Expeditions', 'qrk' ) }>
								<p>{ __( 'Select the Expeditions to be displayed.', 'qrk' ) }</p>
							</Placeholder>
						)
				}
			</Section>
		</>
	);
}
