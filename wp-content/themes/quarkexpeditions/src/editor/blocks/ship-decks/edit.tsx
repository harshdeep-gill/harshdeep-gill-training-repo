/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
} from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

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
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Block properties.
	const blockProps = useBlockProps( {
		className: classnames( className, 'quark-ship-decks' ),
	} );

	// Return the block's markup.
	return (
		<Section { ...blockProps }>
			{
				<ServerSideRender
					block={ name }
					EmptyResponsePlaceholder={ () => (
						<Placeholder
							icon="palmtree"
							label={ __( 'Ship Decks', 'qrk' ) }
							instructions={ __(
								'Select Ship decks to render the data in the frontend.',
								'qrk',
							) }
						/>
					) }
				/>
			}
		</Section>
	);
}
