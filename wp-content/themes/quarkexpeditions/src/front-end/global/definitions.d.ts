/**
 * TypeScript Definitions.
 *
 * Place all your global typescript interface definitions here.
 */

/**
 * Window Object.
 */
interface Window {
	quark?: {
		recaptchaSiteKey?: string;
		fetchPartial?: FetchPartial;
	},
	grecaptcha: {
		ready: Function;
		execute: Function;
	},
	Wistia: {
		api( matcher: String ) : WistiaVideo | null;
	},
	_wq: WistiaVideoMatcher[] | null,
	tpFormValidators: {
		[key: string]: any;
	},
	tpFormErrors: {
		[key: string]: any;
	},
	fetchPartial: Function;
	zustand: {
		persist: Function;
		create: Function;
		stores: {
			[key: string]: {
				getState: Function;
				setState: Function;
				subscribe: Function;
			};
		};
	};
	queryString?: QueryString;
}

/**
 * WistiaVideo interface
 */
interface WistiaVideo {
	play(): void;
	bind( eventName: String, callback: Function ): void;
	pause(): void;
}

/**
 * Video matcher interface.
 */
interface WistiaVideoMatcher { id: String, onReady: Function }

/**
 * FetchPartial interface.
 */
interface FetchPartial {
	url: string,
	method: string,
}

/**
 * Interface CustomException.
 */
interface CustomException {
	code: string;
	message: string;
	data?: any;
}

/**
 * Interface Partial Data.
 */
interface PartialData {
	markup: string;
	noResultsMarkup?: string,
	data: any;
	filtersMarkup?: string;
	compactFiltersMarkup?: string;
}

/**
 * Interface Query String.
 */
interface QueryString {
	stringifyUrl: Function,
	stringify: Function;
	parse: Function;
}

// Global state type.
interface GlobalState {
	currency: string,
	phoneNumberRule: {
		phoneNumber: string,
		prefix: string,
	},
	dynamicPhoneNumber: {
		apiEndpoint: string,
		defaultPhoneNumber: string,
	},
}

/**
 * Event Listener Options interface.
 */
interface AddEventListenerOptions extends EventListenerOptions {
	passive?: boolean;
	once?: boolean;
	signal?: AbortSignal;
}
