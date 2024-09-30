/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { BlockInstance } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
	InnerBlocks,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import * as BlockEditorSelectors from '@wordpress/block-editor/store/selectors';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles
 */
import './editor.scss';

/**
 * Child blocks.
 */
import * as tab from './children/tab';

/**
 * Edit component.
 *
 * @param {Object}   props               Component props.
 * @param {string}   props.className     Block class name.
 * @param {string}   props.clientId      Block client ID.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Block attributes setter.
 */
export default function Edit( {
	className,
	clientId,
	attributes,
	setAttributes,
}: BlockEditAttributes ) {
	// Block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'tabs' ),
	} );

	// Inner blocks props.
	const innerBlockProps = useInnerBlocksProps(
		{
			className: 'tabs__content',
		},
		{
			template: [
				[ tab.name, { title: __( 'Tab 1', 'qrk' ) } ],
				[ tab.name, { title: __( 'Tab 2', 'qrk' ) } ],
				[ tab.name, { title: __( 'Tab 3', 'qrk' ) } ],
			],
			renderAppender: () => null,
			orientation: 'horizontal',
		},
	);

	// Get the inner blocks.
	const innerBlocks = useSelect(
		( select: any ) =>
			( select( blockEditorStore ) as typeof BlockEditorSelectors ).getBlocks(
				clientId,
			),
		[ clientId ],
	);

	// Get dispatcher.
	const { updateBlockAttributes } = useDispatch( blockEditorStore );

	// Set the default tab.
	useEffect( () => {
		// Set the default tab index.
		if ( ! attributes.defaultTabIndex ) {
			setAttributes( { defaultTabIndex: 1 } );
		}

		// Set the active tab client ID.
		if ( ! attributes.activeTabClientId ) {
			handleActiveTabChange( innerBlocks?.[ attributes.defaultTabIndex - 1 ]?.clientId );
		}
	}, [ innerBlocks ] );

	// Get active selected tab block. ( This could be a tab block or a child block of a tab block )
	const activeTabBlockId = useSelect(
		( select: any ) => {
			// Get the selected tab block.
			const selectedBlock = select( blockEditorStore ).getSelectedBlock();

			// Check if there is a selected block.
			if ( ! selectedBlock ) {
				// Return.
				return;
			}

			// Check if the selected block is a tab block.
			if ( selectedBlock?.name === tab.name ) {
				// Return the selected block.
				return selectedBlock.clientId;
			}

			// Check if the selected block is a child block of a tab block.
			const tabBlockIds = select( blockEditorStore ).getBlockParentsByBlockName( selectedBlock.clientId, tab.name );

			// Return the tab block.
			return tabBlockIds?.[ 0 ];
		},
		[],
	);

	// Update the active tab client ID.
	useEffect( () => {
		// Check if the active tab block ID is different from the active tab client ID.
		if ( activeTabBlockId !== attributes.activeTabClientId ) {
			// Set the active tab client ID.
			handleActiveTabChange( activeTabBlockId );
		}
	}, [ activeTabBlockId ] );

	// Handle active tab change.
	const handleActiveTabChange = ( activeTabClientId: string ) => {
		// Set the active tab client ID.
		setAttributes( { activeTabClientId } );
	};

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Tabs Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Default Tab', 'qrk' ) }
						value={ attributes.defaultTabIndex }
						onChange={ ( value ) => setAttributes( { defaultTabIndex: parseInt( value ) } ) }
						options={ innerBlocks.map(
							( innerBlock: BlockInstance, index: number ) => ( {
								label:
									innerBlock.attributes.title ||
									`No title ( tab ${ index + 1 } )`,
								value: ( index + 1 ).toString(),
							} ),
						) }
					/>
					<ToggleControl
						label={ __( 'Update URL?', 'qrk' ) }
						help={ __( 'Update the URL when a tab is clicked.', 'qrk' ) }
						checked={ attributes.updateURL }
						onChange={ ( value ) => setAttributes( { updateURL: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<nav className="tabs__nav">
					{ innerBlocks.map( ( innerBlock: BlockInstance ) => (
						<button
							key={ innerBlock.clientId }
							className={ classnames( 'tabs__nav-item', {
								'tabs__nav-item--active':
									innerBlock.clientId === attributes.activeTabClientId,
							} ) }
							onClick={ () => handleActiveTabChange( innerBlock.clientId ) }
						>
							<b className="tabs__nav-link">
								<RichText
									tagName="span"
									className="tabs__nav-title"
									placeholder={ __( 'Add title…', 'qrk' ) }
									value={ innerBlock.attributes?.title || '' }
									allowedFormats={ [] }
									onChange={ ( value ) =>
										updateBlockAttributes( innerBlock.clientId, {
											title: value,
										} )
									}
								/>
							</b>
						</button>
					) ) }
					<InnerBlocks.ButtonBlockAppender />
				</nav>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
