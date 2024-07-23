/**
 * WordPress dependencies.
 */

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import Section from '../../components/section';

/**
 * Block name.
 */
export const name: string = 'quark/expedition-details';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Return the markup.
	return (
		<>
			<Section className={ classnames( className ) }>
				<ServerSideRender
					block={ name }
				/>
			</Section>
		</>
	);
}
