/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	Placeholder,
} from '@wordpress/components';
import {
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

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
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set Block Props
	const blockProps = useBlockProps( {
		className: classnames( className, 'quark-search-filters-bar' ),
	} );

	// Return the block's markup.
	return (
		<>
			<Section { ...blockProps }>
				<Placeholder icon="layout" label={ __( 'Search Filters Bar', 'qrk' ) }>
					<p>{ __( 'This block will be rendered on the front-end.', 'qrk' ) }</p>
				</Placeholder>
			</Section>
		</>
	);
}
