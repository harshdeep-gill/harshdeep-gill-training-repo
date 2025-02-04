/**
 * Handle error.
 *
 * @param {Event} e event.
 */
const handleError = ( e: CustomException ): void => {
	alert( "There was an error getting this data.\nPlease try again after some time." ); // eslint-disable-line
	throw e;
};

/**
 * Fetch a partial.
 *
 * @param {string}   name     Partial name.
 * @param {Object}   data     Partial data.
 * @param {Function} callback Callback function.
 * @param {Object}   selector Selector for element for use with innerHTML.
 *
 * @return {Promise} Partial data.
 */
window.fetchPartial = async ( name: string = '', data: object = {}, callback: Function, selector: string = '' ): Promise<void> => {
	// Try and catch error.
	try {
		// Throw exception.
		if ( ! window.quark || ! window.quark.fetchPartial ) {
			throw { code: 'no_quark_object', message: 'No Quark object in Window' } as CustomException;
		}

		// Fetch the partial.
		await window.fetch( window.quark.fetchPartial.url ?? '', {
			method: window.quark.fetchPartial.method ?? 'post',
			cache: 'no-cache',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( {
				name,
				data,
			} ),
		} ).then( ( response: Response ): void => {
			// If status is not equal to '200', throw error.
			if ( 200 !== response.status ) {
				throw { code: 'invalid_status', message: 'Invalid status', data: response.status } as CustomException;
			}

			// Return promise.
			response.json().then( ( result ): void => {
				// If both markup and data not present throw error.
				if ( ! result.markup && ! result.data ) {
					throw { code: 'invalid_response', message: 'Invalid response', data: result } as CustomException;
				}

				// Build markup.
				let markup = result.markup;

				// If selector is not empty, build markup.
				if ( '' !== selector ) {
					const temp: HTMLDivElement = document.createElement( 'div' );
					temp.innerHTML = markup;
					const target: Element | null = temp.querySelector( selector );

					// If target element is available, set the markup.
					if ( target ) {
						markup = target.innerHTML;
					} else {
						markup = '';
					}
				}

				// Return callback.
				callback( {
					markup,
					data: result.data,
					noResultsMarkup: result?.noResultsMarkup ?? '',
					filtersMarkup: result?.filtersMarkup ?? '',
					compactFiltersMarkup: result?.compactFiltersMarkup ?? '',
				} as PartialData );
			} );
		} ).catch( ( e ): void => {
			// Throw error.
			throw { code: 'fetch_error', message: 'Error while fetching', data: e } as CustomException;
		} );
	} catch ( e ) {
		handleError( e as CustomException );
	}
};
