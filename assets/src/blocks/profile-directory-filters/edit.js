import { __ } from '@wordpress/i18n';

import { Panel, PanelBody, Spinner } from '@wordpress/components';

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render';

import FakeInput from '../../components/FakeInput';
import FakeSelect from '../../components/FakeSelect';
import FakeButton from '../../components/FakeButton';

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @param  root0
 * @param  root0.attributes
 * @param  root0.setAttributes
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes, setAttributes } ) {
	const blockProps = () => {
		return useBlockProps( {
			className: [ 'directory-filter-form' ],
		} );
	};

	const serverSideAtts = Object.assign( {}, attributes, {
		isEditMode: true,
	} );

	return (
		<div { ...blockProps() }>
			<div className="directory-filter">
				<FakeInput
					isSearch={ true }
					text={ __( 'Search…', 'research-amp' ) }
				/>
			</div>

			<div className="directory-filter filter-by-legend">
				{ __( 'Filter by:', 'research-amp' ) }
			</div>

			<div className="directory-filter">
				<FakeSelect
					text={ __( 'All Research Topics', 'research-amp' ) }
				/>
			</div>

			<div className="directory-filter">
				<FakeSelect text={ __( 'All Subtopics', 'research-amp' ) } />
			</div>

			<div className="directory-filter directory-filters-submit">
				<FakeButton text={ __( 'Apply Filters', 'research-amp' ) } />
			</div>
		</div>
	);
}
