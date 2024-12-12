/**
 * Utility functions.
 */

/**
 * Utility functions.
 */

/**
 * Slide element down.
 *
 * @param {HTMLElement|null} element  Target element.
 * @param {number}           duration Animation duration.
 * @param {Function}         callback Callback function.
 */
export const slideElementDown = ( element: HTMLElement, duration: number = 300, callback: Function = () => {} ) => { // eslint-disable-line
	// Get element height.
	element.style.height = `${ element.scrollHeight }px`;

	// Set timeout.
	setTimeout( () => {
		// Set element's height.
		element.style.height = 'auto';

		// If callback is available, call the function.
		if ( callback ) {
			callback();
		}
	}, duration );
};

/**
 * Slide element up.
 *
 * @param {HTMLElement|null} element  Target element.
 * @param {number}           duration Animation duration.
 * @param {Function}         callback Callback function.
 */
export const slideElementUp = ( element: HTMLElement, duration: number = 300, callback: Function = () => {} ) => { // eslint-disable-line
	// Get element height.
	element.style.height = `${ element.scrollHeight }px`;
	element.offsetHeight; // eslint-disable-line
	element.style.height = '0px';

	// Set timeout.
	setTimeout( () => {
		// Set element's height.
		element.style.height = '0px';

		// If callback is available, call the function.
		if ( callback ) {
			callback();
		}
	}, duration );
};

/**
 * Debounce Function.
 *
 * @param {Function} func  Function
 * @param {number}   delay Delay in ms
 * @return {(function(): void)|*} Debounce Function.
 */
export const debounce = ( func: Function, delay = 300 ) => {
	// Prepare timer.
	let debounceTimer: ReturnType<typeof setTimeout>;

	// Return the debounce function.
	return ( ...args: any ) => {
		// Clear current timer and set a new one.
		clearTimeout( debounceTimer );
		debounceTimer = setTimeout( () => {
			// Call the callback function.
			func.apply( this, args );
		}, delay );
	};
};

/**
 * Camel To Snake Case
 *
 * @param {string} camelCaseString Camel Case String.
 */
export const camelToSnakeCase = ( camelCaseString: string ): string => {
	// Check if camelCaseString is empty.
	if ( ! camelCaseString ) {
		// Early return.
		return '';
	}

	// Replace each uppercase letter with an underscore followed by the lowercase version of the letter.
	return camelCaseString.replace( /([A-Z])/g, '_$1' ).toLowerCase();
};

/**
 * Snake To Camel Case
 *
 * @param {string} snakeCaseString Snake Case String.
 */
const snakeToCamelCase = ( snakeCaseString: string ): string => {
	// Convert snake case to camel case.
	return snakeCaseString.toLowerCase().replace( /(_\w)/g, ( match ) => {
		// Return the matched value.
		return match[ 1 ].toUpperCase();
	} );
};

/**
 * Convert all object properties from snake_case to camelCase.
 *
 * @param {Object} data Data.
 */
export const convertPropertiesFromSnakeCaseToCamelCase = ( data: object = {} ): object => {
	// Recursive function to convert the snake_case to camelCase
	const convert = ( theObject: { [ key: string ]: any } ): { [ key: string ]: any } => {
		// Check if it's an object.
		if ( typeof theObject === 'object' ) {
			// Return the converted object.
			return Object.keys( theObject ).reduce<{ [ key: string ]: any }>( ( accumulatedObject, key ) => {
				// Get the cameCaseKey.
				const camelCaseKey: string = snakeToCamelCase( key );

				// Set the value of the property after converting it to camelCase.
				accumulatedObject[ camelCaseKey ] = convert( theObject[ key ] );

				// Return the converted object.
				return accumulatedObject;
			}, {} as Object );
		}

		// Return the object.
		return theObject;
	};

	// Return the new converted object.
	return convert( data );
};

/**
 * Throttle a callback function.
 *
 * @param {Function} callbackFunction Callback function.
 * @param {number}   delay            Delay in ms.
 */
export const throttle = ( callbackFunction: Function, delay: number = 100 ) => {
	// Timer flag.
	let timerFlag: null | NodeJS.Timeout = null;

	// Returning a throttled version
	return ( ...args: any ) => {
		// If the timerFlag is null, execute the main function
		if ( timerFlag === null ) {
			// Execute the callback function
			callbackFunction( ...args );

			// Reset.
			timerFlag = setTimeout( () => {
				timerFlag = null; // Clear the timerFlag after the delay
			}, delay );
		}
	};
};
