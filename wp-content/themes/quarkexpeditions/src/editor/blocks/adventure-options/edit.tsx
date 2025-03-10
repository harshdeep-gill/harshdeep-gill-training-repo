/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	SelectControl,
	Placeholder,
	Tooltip,
	RangeControl,
	ToggleControl,
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
const {
	PostRelationshipControl,
	TaxonomyRelationshipControl,
} = gumponents.components;

/**
 * Styles.
 */
import './editor.scss';

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
				<PanelBody title={ __( 'Adventure Options Settings', 'qrk' ) }>
					<SelectControl
						label={ __( 'Selection', 'qrk' ) }
						help={ __( 'Select how you would like to select posts', 'qrk' ) }
						value={ attributes.selectionType }
						options={ [
							{ label: __( 'Automatic', 'qrk' ), value: 'auto' },
							{ label: __( 'Manual', 'qrk' ), value: 'manual' },
							{ label: __( 'By Category', 'qrk' ), value: 'byCategory' },
						] }
						onChange={ ( selectionType: string ) => setAttributes( { selectionType } ) }
					/>
					{
						// If automatic and no destination is selected, show a tooltip.
						'auto' === attributes.selectionType &&
						<Tooltip>
							<p>{ __( 'Please select Destination(s) for this block to display adventure options.', 'qrk' ) }</p>
						</Tooltip>
					}
					{
						'manual' === attributes.selectionType &&
						<PostRelationshipControl
							label={ __( 'Select Adventure Options', 'qrk' ) }
							help={ __( 'Select Adventure Options', 'qrk' ) }
							postTypes="qrk_adventure_option"
							value={ attributes.ids }
							onSelect={ ( postIDs: any ) => setAttributes( { ids: postIDs.map( ( post: any ) => post.ID ) } ) }
							button={ __( 'Select Adventure Options', 'qrk' ) }
						/>
					}
					{
						'byCategory' === attributes.selectionType &&
						<TaxonomyRelationshipControl
							label={ __( 'Select Adventure Option Category.', 'et' ) }
							help={ __( 'Select Adventure Option Category', 'et' ) }
							taxonomies="qrk_adventure_option_category"
							value={ attributes.termIDs }
							onSelect={ ( terms: Array<{ term_id: number }> ) => setAttributes( { termIDs: terms.map( ( term ) => term.term_id ) } ) }
							buttonLabel={ __( 'Select Adventure Option Category', 'qrk' ) }
						/>
					}
					{	'manual' !== attributes.selectionType &&
						<RangeControl
							label={ __( 'Total Posts', 'qrk' ) }
							help={ __( 'Select the total number of options to be displayed', 'qrk' ) }
							value={ attributes.total }
							onChange={ ( total ) => setAttributes( { total } ) }
							min={ 1 }
							max={ 20 }
						/>
					}
					<ToggleControl
						label={ __( 'Allow overflow?', 'qrk' ) }
						checked={ attributes.carouselOverflow }
						onChange={ ( carouselOverflow ) => setAttributes( { carouselOverflow } ) }
						help={ __( 'Allow overflow of cards horizontally', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section className={ classnames( className ) }>
				<ServerSideRender
					block={ name }
					attributes={ attributes }
					EmptyResponsePlaceholder={ () => (
						<Placeholder
							icon="location-alt"
							label={ __( 'Adventure Options', 'qrk' ) }
							instructions={ __(
								'Please select a way to display Adventure Options.',
								'qrk',
							) }
						/>
					) }
				/>
			</Section>
		</>
	);
}
