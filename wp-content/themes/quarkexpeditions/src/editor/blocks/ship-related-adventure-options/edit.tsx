/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies.
 */
import metadata from './block.json';

/**
 * External dependencies.
 */
import classNames from 'classnames';
import Section from '../../components/section';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Edit Component.
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) {
	// Get block props.
	const blockProps = useBlockProps( {
		className: classNames( className, '' ),
	} );

	//
	return (
		<Section { ...blockProps }>
			<ServerSideRender
				block={ name }
				EmptyResponsePlaceholder={ () => (
					<Placeholder
						icon="palmtree"
						label={ __( 'Ship Related Adventure Options', 'qrk' ) }
						instructions={ __(
							'Please select one or more adventure options.',
							'qrk',
						) }
					/>
				) }
			/>
		</Section>
	);
}
