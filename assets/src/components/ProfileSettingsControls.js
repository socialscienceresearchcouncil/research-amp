import {
	Button,
	PanelRow,
	TextControl,
	ToggleControl
} from '@wordpress/components'

import { __ } from '@wordpress/i18n'
import { useDispatch, useSelect } from '@wordpress/data'
import { PluginDocumentSettingPanel } from '@wordpress/edit-post'

import { usePrevious } from '@wordpress/compose'

import { dispatch, select } from '@wordpress/data'
import { store } from '@wordpress/editor'

export default function ProfileSettingsControls( {
	isSelected
} ) {
	const postType = select( 'core/editor' ).getCurrentPostType();
	if ( 'ramp_profile' !== postType ) {
		return null;
	}

	const { editPost } = useDispatch( 'core/editor' )

	const {
		alphabeticalName
	} = useSelect(
		( select ) => {
			const { getCurrentPostAttribute, getEditedPostAttribute } = select( store )

			return {
				alphabeticalName: getEditedPostAttribute( 'meta' ).alphabetical_name,
			};
		},
		[]
	);

	const editPostMeta = ( metaToUpdate ) => {
		editPost( { meta: metaToUpdate } )
	}

	return (
		<PluginDocumentSettingPanel
			name="ramp-profile-settings"
			title={ __( 'Profile Settings', 'research-amp' ) }
			>

			<PanelRow>
				<TextControl
					label={ __( 'Name for Alphabetical Sorting', 'research-amp' ) }
					help={ __( "To order by last name, enter the individual's last name, followed by the first.", 'research-amp' ) }
					onChange={ ( alphabeticalName ) => editPostMeta( { 'alphabetical_name': alphabeticalName } ) }
					value={ alphabeticalName }
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
