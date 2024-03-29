import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

import Select from 'react-select';

import { unescapeString } from './ReorderableFlatTermSelector/utils';

const ProfileSelector = ( props ) => {
	const { disabled = false, onChangeCallback, selectedProfileId } = props;

	const { profiles } = useSelect( ( select ) => {
		const profiles = select( 'research-amp' ).getProfiles();

		return {
			profiles,
		};
	}, [] );

	const profilesOptions = profiles
		? profiles.map( ( profile ) => {
				return {
					label: unescapeString( profile.title.rendered ),
					value: profile.id,
				};
		  } )
		: [];

	const selectedOption = profiles.find(
		( option ) => option.value === selectedProfileId
	);

	const handleChange = ( selected ) => {
		const newValue = selected ? selected.value : 0;
		onChangeCallback( newValue );
	};

	return (
		<>
			<label className="screen-reader-text">
				{ __( 'Select a Profile', 'research-amp' ) }
			</label>

			<Select
				controlShouldRenderValue={ true }
				isClearable={ true }
				isDisabled={ disabled }
				menuPortalTarget={ document.querySelector( 'body' ) }
				onChange={ handleChange }
				options={ profilesOptions }
				placeholder={ __( 'Select a Profile', 'research-amp' ) }
				value={ selectedOption }
			/>
		</>
	);
};

export default ProfileSelector;
