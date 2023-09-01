import {
	PanelRow,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

import { __ } from '@wordpress/i18n';
import { select, useDispatch, useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { store } from '@wordpress/editor';

export default function ProfileSettingsControls( {} ) {
	const postType = select( 'core/editor' ).getCurrentPostType();

	const { editPost, lockPostSaving, unlockPostSaving } = useDispatch( 'core/editor' );

	const { removeNotice, createNotice } = useDispatch( 'core/notices' );

	const { alphabeticalName } = useSelect( ( dataSelect ) => {
		const { getEditedPostAttribute } = dataSelect( store );

		return {
			alphabeticalName: getEditedPostAttribute( 'meta' ).alphabetical_name,
		};
	}, [] );

	if ( 'ramp_profile' !== postType ) {
		return null;
	}

	if ( alphabeticalName.length > 0 ) {
		setTimeout( () => {
			unlockPostSaving( 'ramp_profile' );
			removeNotice( 'ramp_profile' );
		}, 500 );
	} else {
		setTimeout( () => {
			lockPostSaving( 'ramp_profile' );
			createNotice(
				'error',
				__( 'You must enter an Alphabetical Name for this profile.', 'research-amp' ),
				{ id: 'ramp_profile', isDismissable: false }
			);
		}, 500 );
	}

	const editPostMeta = ( metaToUpdate ) => {
		editPost( { meta: metaToUpdate } );
	};

	return (
		<PluginDocumentSettingPanel
			name="ramp-profile-settings"
			title={ __( 'Profile Settings', 'research-amp' ) }
		>
			<PanelRow>
				<TextControl
					label={ __( 'Name for Alphabetical Sorting', 'research-amp' ) }
					help={ __( "To order by last name, enter the individual's last name, followed by the first.", 'research-amp') }
					onChange={ ( newAlphabeticalName ) =>
						editPostMeta( { alphabetical_name: newAlphabeticalName } )
					}
					value={ alphabeticalName }
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
