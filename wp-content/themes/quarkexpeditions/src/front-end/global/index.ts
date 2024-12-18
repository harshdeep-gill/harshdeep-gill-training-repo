/**
 * Styles.
 */
import './index.scss';

/**
 * Internal dependencies.
 */
import './dom-content-loaded';

/**
 * Fetch Partial.
 */
import './fetch-partial';

/**
 * Zustand.
 */
import './zustand';

/**
 * Global Store.
 */
import './store';
import { initialize } from './store/actions';

/**
 * Global JavaScript.
 */
import './phone-number';
import './tracking';
import './performance';

/**
 * TypeScript Stuff.
 */
declare global {
	interface Window {
		dynamicPhoneNumber: {
			api_endpoint?: string; // eslint-disable-line
			default_phone_number?: string; // eslint-disable-line
		};
		yieldToMain: () => Promise<void>;
		addEventListenerWithYieldToMain: ( element: HTMLElement, event: string, callback: () => void, options: boolean | AddEventListenerOptions ) => void;
	}
}

/**
 * Initialize global store.
 */
initialize( {} );
