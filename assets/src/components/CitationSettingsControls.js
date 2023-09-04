import {
	PanelRow,
	ToggleControl,
} from '@wordpress/components';

import { __ } from '@wordpress/i18n';
import { select, useDispatch, useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { store } from '@wordpress/editor';

export default function CitationSettingsControls( {} ) {
	const postType = select( 'core/editor' ).getCurrentPostType();

	const { editPost } = useDispatch( 'core/editor' );

	const { isFeatured } = useSelect( ( dataSelect ) => {
		const { getEditedPostAttribute } = dataSelect( store );

		const editedPostMeta = getEditedPostAttribute( 'meta' );

		// getEditedPostTypeAttribute is not available in the Site Editor.
		const savedIsFeatured = postType && editedPostMeta ? editedPostMeta.is_featured : false;

		return {
			isFeatured: savedIsFeatured,
		};
	}, [] );

	if ( 'ramp_citation' !== postType ) {
		return null;
	}

	const editPostMeta = ( metaToUpdate ) => {
		editPost( { meta: metaToUpdate } );
	};

	return (
		<PluginDocumentSettingPanel
			name="ramp-citation-settings"
			title={ __( 'Citation Settings', 'research-amp' ) }
		>
			<PanelRow>
				<ToggleControl
					label={ __( 'Featured Profile', 'research-amp' ) }
					help={ __( 'Show this profile in the Featured Citations section.', 'research-amp' ) }
					checked={ !! isFeatured }
					onChange={ ( newIsFeatured ) => {
						editPostMeta( { is_featured: newIsFeatured } );
					} }
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
