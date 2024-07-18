/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	Placeholder,
} from '@wordpress/components';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

// Child blocks.
import * as subRegion from '../sub-region';
import * as mostImportantFactors from '../most-important-factors';
import * as paxCount from '../pax-count';
import * as season from '../season';
import * as expeditionName from '../expedition-name';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( { className } );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {},
		{
			allowedBlocks: [
				subRegion.name, mostImportantFactors.name, paxCount.name, season.name, expeditionName.name,
			],
			template: [
				[ subRegion.name ], [ mostImportantFactors.name ], [ paxCount.name ], [ season.name ],
			],
		}
	);

	// Return the block's markup.
	return (
		<>
			<div { ...blockProps }>
				<Placeholder
					label={ __( 'Two Step Form - Step One Landing Form', 'qrk' ) }
					icon="layout"
				>
					<p>{ __( 'This form will render on the front-end.', 'qrk' ) }</p>
					<div { ...innerBlockProps } />
				</Placeholder>
			</div>
		</>
	);
}
