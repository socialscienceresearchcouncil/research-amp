import apiFetch from '@wordpress/api-fetch'
import { registerStore } from '@wordpress/data'

const DEFAULT_STATE = {
	libraries: {}
}

const STORE_NAME = 'ramp'

const actions = {
	fetchFromAPI( path ) {
		return {
			type: 'FETCH_FROM_API',
			path
		}
	},

	setLibraryInfo( libraryId, libraryInfo ) {
		return {
			type: 'SET_LIBRARY_INFO',
			libraryId,
			libraryInfo
		}
	}
}

const reducer = ( state = DEFAULT_STATE, action ) => {
	switch ( action.type ) {
		case 'SET_LIBRARY_INFO' :
			return {
				...state,
				libraries: {
					...state.libraries,
					[ action.libraryId ]: action.libraryInfo
				}
			}

		default :
			return state
	}
}

const controls = {
	FETCH_FROM_API( action ) {
		return apiFetch( { path: action.path } )
	},
}

const selectors = {
	getLibraryInfo( state, libraryId ) {
		const { libraries } = state
		const libraryInfo = libraries[ libraryId ]

		return libraryInfo
	}
}

const resolvers = {
	*getLibraryInfo( libraryId ) {
		const path = '/ramp/v1/zotero-library/' + libraryId
		const libraryInfo = yield actions.fetchFromAPI( path )
		return actions.setLibraryInfo( libraryId, libraryInfo )
	}
}

const storeConfig = {
	actions,
	reducer,
	controls,
	selectors,
	resolvers
}

registerStore( STORE_NAME, storeConfig )

export { STORE_NAME }
