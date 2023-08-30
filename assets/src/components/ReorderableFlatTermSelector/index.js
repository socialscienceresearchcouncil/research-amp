/**
 * Based on FlatTermSelector from WordPress.
 */

import { find, get } from 'lodash';

import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { useDebounce } from '@wordpress/compose';

import { store as editorStore } from '@wordpress/editor';
import { store as coreStore } from '@wordpress/core-data';
import { speak } from '@wordpress/a11y';

import { unescapeString } from './utils';
import SortableMultiSelect from '../SortableMultiSelect';

const EMPTY_ARRAY = [];

const MAX_TERMS_SUGGESTIONS = 20;
const DEFAULT_QUERY = {
	per_page: MAX_TERMS_SUGGESTIONS,
	orderby: 'count',
	order: 'desc',
	_fields: 'id,name',
	context: 'view',
};

const isSameTermName = ( termA, termB ) =>
	unescapeString( termA ).toLowerCase() ===
	unescapeString( termB ).toLowerCase();

const termNamesToIds = ( names, terms ) => {
	return names.map(
		( termName ) =>
			find( terms, ( term ) => isSameTermName( term.name, termName ) ).id
	);
};

function ReorderableFlatTermSelector( { slug } ) {
	const [ values, setValues ] = useState( [] );
	const [ search, setSearch ] = useState( '' );
	const debouncedSearch = useDebounce( setSearch, 500 );

	const {
		terms,
		termIds,
		taxonomy,
		hasAssignAction,
		hasCreateAction,
		hasResolvedTerms,
	} = useSelect(
		( select ) => {
			const { getCurrentPost, getEditedPostAttribute } =
				select( editorStore );
			const { getEntityRecords, getTaxonomy, hasFinishedResolution } =
				select( coreStore );
			const post = getCurrentPost();

			const _taxonomy = getTaxonomy( slug );
			const _termIds = _taxonomy
				? getEditedPostAttribute( _taxonomy.rest_base )
				: EMPTY_ARRAY;

			const query = {
				...DEFAULT_QUERY,
				include: _termIds.join( ',' ),
				per_page: -1,
			};

			return {
				taxonomy: _taxonomy,
				termIds: _termIds,
				terms: _termIds.length
					? getEntityRecords( 'taxonomy', slug, query )
					: EMPTY_ARRAY,
				hasResolvedTerms: hasFinishedResolution( 'getEntityRecords', [
					'taxonomy',
					slug,
					query,
				] ),
			};
		},
		[ slug ]
	);

	const { availableTerms } = useSelect(
		( select ) => {
			const { getEntityRecords } = select( coreStore );

			const allTerms = getEntityRecords( 'taxonomy', slug, {
				per_page: -1,
				orderby: 'name',
				order: 'asc',
				_fields: 'id,name',
				context: 'view',
			} );

			// Transform to match format expected by react-select
			let availableTerms = EMPTY_ARRAY;
			if ( allTerms !== null ) {
				availableTerms = allTerms.map( ( result ) => {
					return {
						label: unescapeString( result.name ),
						value: result.id,
					};
				} );
			}

			return {
				availableTerms,
			};
		},
		[ hasResolvedTerms ]
	);

	const selectedTerms =
		!! availableTerms && !! termIds && availableTerms.length > 0
			? termIds.map( ( termId ) => {
					return availableTerms.find(
						( term ) => termId === term.value
					);
			  } )
			: EMPTY_ARRAY;

	// Update terms state only after the selectors are resolved.
	// We're using this to avoid terms temporarily disappearing on slow networks
	// while core data makes REST API requests.
	useEffect( () => {
		if ( hasResolvedTerms ) {
			const newValues = terms.map( ( term ) =>
				unescapeString( term.name )
			);

			setValues( newValues );
		}
	}, [ terms, hasResolvedTerms ] );

	const { editPost } = useDispatch( editorStore );

	function onUpdateTerms( newTermIds ) {
		editPost( { [ taxonomy.rest_base ]: newTermIds } );
	}

	const onChange = ( changed ) => {
		const newTermIds = changed.map( ( item ) => item.value );
		onUpdateTerms( newTermIds );
	};

	return (
		<>
			<SortableMultiSelect
				options={ availableTerms }
				selectedOptions={ selectedTerms }
				onChange={ onChange }
			/>
		</>
	);
}

export default ReorderableFlatTermSelector;
