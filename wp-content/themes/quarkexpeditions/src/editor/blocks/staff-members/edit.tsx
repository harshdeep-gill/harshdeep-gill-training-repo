/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	ToggleControl,
	Placeholder,
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
} = gumponents.components;

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
	// Initialize.
	let allOptionsSelected = false;

	// Check if all required options are selected.
	if ( 'recent' === attributes.selection && 0 !== attributes.departmentIds.length ) {
		allOptionsSelected = true;
	} else if ( 'manual' === attributes.selection && 0 !== attributes.ids.length ) {
		allOptionsSelected = true;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Staff Members Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Selection', 'qrk' ) }
						help={ __( 'Select how you would like to select posts', 'qrk' ) }
						value={ attributes.selection }
						options={ [
							{ label: __( 'Manual', 'qrk' ), value: 'manual' },
							{ label: __( 'Recent', 'qrk' ), value: 'recent' },
						] }
						onChange={ ( selection ) => setAttributes( { selection } ) }
					/>
					{ 'manual' === attributes.selection &&
						<PostRelationshipControl
							label={ __( 'Select Staff Members', 'qrk' ) }
							help={ __( 'Select Staff Members', 'qrk' ) }
							postTypes="qrk_staff_member"
							value={ attributes.ids }
							onSelect={ ( staffMembersPosts: any ) => setAttributes( { ids: staffMembersPosts.map( ( post: any ) => post.ID ) } ) }
							button={ __( 'Select Staff Members', 'qrk' ) }
						/>
					}
					{ 'recent' === attributes.selection &&
						<>
							<TaxonomyRelationshipControl
								label={ __( 'Select Staff Members Department', 'qrk' ) }
								help={ __( 'Select Staff Members Department', 'qrk' ) }
								taxonomies="qrk_department"
								value={ attributes.departmentIds }
								onSelect={ ( departments: Array<{ term_id: number }> ) => setAttributes( { departmentIds: departments.map( ( department ) => department.term_id ) } ) }
								buttonLabel={ __( 'Select Department', 'qrk' ) }
							/>
							<RangeControl
								label={ __( 'Total Posts', 'qrk' ) }
								help={ __( 'Select the total number of members to be displayed', 'qrk' ) }
								value={ attributes.totalPosts }
								onChange={ ( totalPosts ) => setAttributes( { totalPosts } ) }
								min={ 1 }
								max={ 20 }
							/>
						</>
					}
					<ToggleControl
						label={ __( 'Is Carousel?', 'qrk' ) }
						checked={ attributes.isCarousel }
						help={ __( 'Show carousel navigation arrows in desktop', 'qrk' ) }
						onChange={ ( isCarousel: boolean ) => setAttributes( { isCarousel } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section className={ classnames( className ) }>
				{
					allOptionsSelected ? (
						<ServerSideRender
							block={ name }
							attributes={ attributes }
						/>
					) : (
						<Placeholder icon="layout" label={ __( 'Staff Members', 'qrk' ) }>
							<p>{ __( 'Select a few Staff Members.', 'qrk' ) }</p>
						</Placeholder>
					)
				}
			</Section>
		</>
	);
}
