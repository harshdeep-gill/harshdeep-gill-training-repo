/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	SelectControl,
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
const { PostRelationshipControl } = gumponents.components;

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
	if ( 'recent' === attributes.selection ) {
		allOptionsSelected = true;
	} else if ( 'manual' === attributes.selection && 0 !== attributes.ids.length ) {
		allOptionsSelected = true;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Related Posts Options', 'qrk' ) }>
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
							label={ __( 'Select Blog Posts', 'qrk' ) }
							help={ __( 'Select blog posts', 'qrk' ) }
							postTypes="post"
							value={ attributes.ids }
							onSelect={ ( blogPosts: any ) => setAttributes( { ids: blogPosts.map( ( post: any ) => post.ID ) } ) }
							button={ __( 'Select Blog Posts', 'qrk' ) }
						/>
					}
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
						<Placeholder icon="layout" label={ __( 'Related Posts', 'qrk' ) }>
							<p>{ __( 'Select the related posts.', 'qrk' ) }</p>
						</Placeholder>
					)
				}
			</Section>
		</>
	);
}
