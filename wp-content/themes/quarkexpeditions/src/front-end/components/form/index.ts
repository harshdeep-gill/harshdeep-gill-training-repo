/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency.
 */
import '@travelopia/web-components/dist/form';

/**
 * Form Class.
 */
export default class Form extends HTMLElement {
}

/**
 * Initialize.
 */
customElements.define( 'quark-form', Form );
