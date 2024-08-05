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
