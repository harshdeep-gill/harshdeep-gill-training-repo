import { useBlockProps } from "@wordpress/block-editor";
import classNames from "classnames";

/**
 * Edit Component.
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 * @returns 
 */
export default function Edit( { className }: BlockEditAttributes ) {
    const blockProps = useBlockProps( {
		className: classNames( className, 'header', 'full-width' ),
	} );

    return (
        <div { ...blockProps }>
            <p>Secondary Navigation</p>
        </div>
    );
}