/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	Placeholder,
} from '@wordpress/components';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/form-two-step/style.scss';

// Child blocks.
import * as subRegion from './sub-region';
import * as mostImportantFactors from './most-important-factors';
import * as paxCount from './pax-count';
import * as season from './season';
import * as expeditionName from './expedition-name';

// Register child blocks.
registerBlockType( subRegion.name, subRegion.settings );
registerBlockType( mostImportantFactors.name, mostImportantFactors.settings );
registerBlockType( paxCount.name, paxCount.settings );
registerBlockType( season.name, season.settings );
registerBlockType( expeditionName.name, expeditionName.settings );

/**
 * Block name.
 */
export const name: string = 'quark/form-two-step-landing-form';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Two Step Landing Form', 'qrk' ),
	description: __( 'Display the first step - Landing Form', 'qrk' ),
	category: 'forms',
	parent: [ 'quark/form-two-step' ],
	keywords: [
		__( 'landing', 'qrk' ),
		__( 'form', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
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
	},
	save() {
		// Don't save anything.
		return <InnerBlocks.Content />;
	},
};
