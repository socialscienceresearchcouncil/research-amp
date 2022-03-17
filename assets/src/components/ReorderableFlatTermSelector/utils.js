import { map, unescape as lodashUnescapeString } from 'lodash';

// Lodash unescape function handles &#39; but not &#039; which may be return in some API requests.
export const unescapeString = ( arg ) => {
	return lodashUnescapeString( arg.replace( '&#039;', "'" ) );
};

/**
 * Returns a term object with name unescaped.
 * The unescape of the name property is done using lodash unescape function.
 *
 * @param {Object} term The term object to unescape.
 *
 * @return {Object} Term object with name property unescaped.
 */
export const unescapeTerm = ( term ) => {
	return {
		...term,
		name: unescapeString( term.name ),
	};
};

/**
 * Returns an array of term objects with names unescaped.
 * The unescape of each term is performed using the unescapeTerm function.
 *
 * @param {Object[]} terms Array of term objects to unescape.
 *
 * @return {Object[]} Array of term objects unescaped.
 */
export const unescapeTerms = ( terms ) => {
	return map( terms, unescapeTerm );
};
