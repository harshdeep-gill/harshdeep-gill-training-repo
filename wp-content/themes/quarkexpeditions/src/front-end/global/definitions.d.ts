/**
 * TypeScript Definitions.
 *
 * Place all your global typescript interface definitions here.
 * e.g.
 * interface MultiSelectItem {
 * 	id: number;
 * 	choiceId: number;
 * 	groupId: number;
 * 	value: string;
 * 	label: string;
 * }
 */

/**
 * Intl Tel Input.
 */
interface intlTelInput<intlTelInput> {
	getNumber: Function;
	isValidNumber: Function;
	getValidationError: Function;
	setNumber: Function;
	promise: {
		then: Function
	};

	// eslint-disable-next-line @travelopia/wordpress-coding-standards/comment-before-code-block
	new( ...args: any ): intlTelInput;
}

/**
 * Visitor Info.
 */
interface VisitorInfo {
	'email': string,
	'full_name'?: string,
	'firstname'?: string,
	'lastname'?: string,
	'phone'?: string
}

/**
 * Glightbox.
 */
interface Glightbox<GLightbox> {
	new( ...args: any ): GLightbox;

	// eslint-disable-next-line @travelopia/wordpress-coding-standards/comment-before-code-block
	openAt: Function;
	sourceElement: Object;
	on: Function;
	open: Function;
	slidesContainer: HTMLElement;
	modal: HTMLElement;
	elements: Array<Object>;
	index: number,
}

/**
 * Interface Query String.
 */
interface queryString<queryString> {
	stringifyUrl: Function,
	stringify: Function;
	parse: Function;
}

/**
 * Window Object.
 */
interface Window {
	GLightbox?: Glightbox<any>;
	intlTelInput?: intlTelInput<any>;
	quark?: {
		recaptchaSiteKey?: string;
		fetchPartial?: {
			url?: string;
			method?: string;
		};
	},
	fetchPartial: Function;
	queryString?: queryString<any>;
	grecaptcha: {
		ready: Function;
		execute: Function;
	},
	zustand: {
		persist: Function;
		create: Function;
		stores: {
			[ key: string ]: {
				getState: Function;
				setState: Function;
				subscribe: Function;
			};
		};
	};
}
