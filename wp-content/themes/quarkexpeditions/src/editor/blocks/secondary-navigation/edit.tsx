/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";

/**
 * External dependencies.
 */
import classNames from "classnames";

/**
 * Child blocks.
 */
import * as secondaryNavigationMenu from './children/secondary-navigation-menu';

/**
 * Edit Component.
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 * @returns 
 */
export default function Edit( { className }: BlockEditAttributes ) {
    const blockProps = useBlockProps( {
		className: classNames( className, 'secondary-navigation__wrap' ),
	} );

    const innerBlockProps = useInnerBlocksProps(
        { ...blockProps },
        {
            allowedBlocks: [ secondaryNavigationMenu.name ],
            template: [ [ secondaryNavigationMenu.name ] ],
            orientation: 'horizontal',
        }
    );

    return (
        <div { ...innerBlockProps } />
    );
}