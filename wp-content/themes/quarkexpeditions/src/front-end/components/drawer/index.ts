/**
 * Internal dependencies.
 */
import { QuarkDrawerElement } from './drawer';
import { QuarkDrawerCloseElement } from './drawer-close';
import { QuarkDrawerOpenElement } from './drawer-open';

/**
 * Initialize.
 */
customElements.define( 'quark-drawer', QuarkDrawerElement );
customElements.define( 'quark-drawer-close', QuarkDrawerCloseElement );
customElements.define( 'quark-drawer-open', QuarkDrawerOpenElement );
