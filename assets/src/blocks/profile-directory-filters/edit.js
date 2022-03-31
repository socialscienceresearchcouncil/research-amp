import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	Spinner
} from '@wordpress/components'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render'

import FakeInput from '../../components/FakeInput'
import FakeSelect from '../../components/FakeSelect'
import FakeButton from '../../components/FakeButton'

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @return {WPElement} Element to render.
 */
export default function edit( {
	attributes,
	setAttributes,
} ) {
	const blockProps = () => {
		return useBlockProps( {
			className: []
		} )
	}

	const serverSideAtts = Object.assign( {}, attributes, { isEditMode: true } )

	return (
		<div { ...blockProps() }>
			<div className="directory-filter">
				<FakeInput isSearch={ true } text={ __( 'Search...', 'ramp' ) } />
			</div>

			<div className="directory-filter filter-by-legend">
				{ __( 'Filter by:', 'ramp' ) }
			</div>

			<div className="directory-filter">
				<FakeSelect text={ __( 'All Research Topics', 'ramp' ) } />
			</div>

			<div className="directory-filter">
				<FakeSelect text={ __( 'All Subtopics', 'ramp' ) } />
			</div>

			<div className="directory-filter directory-filter-submit">
				<FakeButton text={ __( 'Apply Filters', 'ramp' ) } />
			</div>
		</div>
	)
}
