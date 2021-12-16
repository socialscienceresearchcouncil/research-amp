import apiFetch from '@wordpress/api-fetch'
import { registerStore } from '@wordpress/data'

const DEFAULT_STATE = {
	articles: [],
	libraries: {},
	researchTopics: []
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
	},

	setArticles( articles ) {
		return {
			type: 'SET_ARTICLES',
			articles
		}
	},

	setResearchTopics( researchTopics ) {
		return {
			type: 'SET_RESEARCH_TOPICS',
			researchTopics
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

		case 'SET_ARTICLES' :
			return {
				...state,
				articles: action.articles
			}

		case 'SET_RESEARCH_TOPICS' :
			return {
				...state,
				researchTopics: action.researchTopics
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
	getArticles( state ) {
		const { articles } = state
		return articles
	},

	getLibraryInfo( state, libraryId ) {
		const { libraries } = state
		const libraryInfo = libraries[ libraryId ]

		return libraryInfo
	},

	getResearchTopics( state ) {
		const { researchTopics } = state

		return researchTopics
	},
}

const resolvers = {
	*getLibraryInfo( libraryId ) {
		const path = '/ramp/v1/zotero-library/' + libraryId
		const libraryInfo = yield actions.fetchFromAPI( path )
		return actions.setLibraryInfo( libraryId, libraryInfo )
	},

	*getResearchTopics() {
		const path = '/wp/v2/research-topics?per_page=50&orderby=title&order=asc&context=edit'
		const researchTopics = yield actions.fetchFromAPI( path )
		return actions.setResearchTopics( researchTopics )
	},

	*getArticles() {
		const path = '/wp/v2/articles?per_page=100&orderby=title&order=asc'
		const articles = yield actions.fetchFromAPI( path )
		return actions.setArticles( articles )
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
