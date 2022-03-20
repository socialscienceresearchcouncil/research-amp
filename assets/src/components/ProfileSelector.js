import { __ } from '@wordpress/i18n'
import { useSelect } from '@wordpress/data'

import { PostPicker } from './PostPicker'
import { unescapeString } from './ReorderableFlatTermSelector/utils'

const ProfileSelector = ( props ) => {
	const {
		onChangeCallback,
		selectedProfileId
	} = props

	const { post } = useSelect( ( select ) => {
		let post = {}
		if ( selectedProfileId ) {
			post = select( 'ramp' ).getPost( selectedProfileId, 'profiles' )
		}

		return {
			post
		}
	}, [ selectedProfileId ] )

	const profileName = 'undefined' !== typeof post && post.hasOwnProperty( 'title' ) ? post.title.rendered : ''

	return (
		<>
			<PostPicker
				postTypes={ [ 'profiles' ] }
				onSelectPost={ onChangeCallback }
				label={ __( 'Profile', 'ramp' ) }
				placeholder={ __( 'Start typing to search.', 'ramp' ) }
			/>

			{profileName &&
				<div className="profile-selector-selected">
					<button
						className="profile-selector-clear-selected"
						onClick={ () => { onChangeCallback( { id: 0 } ) } }
					>
						<span className="screen-reader-text">{ __( 'Unselect Profile', 'ramp' ) }</span>
					</button>
					{profileName}
				</div>
			}
		</>
	)
}

export default ProfileSelector
