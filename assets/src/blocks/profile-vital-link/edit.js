import { __ } from '@wordpress/i18n';

import {
	URLInput,
	URLPopover,
	useBlockProps
} from '@wordpress/block-editor';

import {
	dispatch,
	useSelect
} from '@wordpress/data';

import { find } from 'lodash';
import variations from './variations';

import {
	Button,
	TextControl
} from '@wordpress/components'

import {
	useRef
} from '@wordpress/element'

import { EmailIcon } from './icons/email'

/**
 * Editor styles.
 */
import './editor.scss';

const getIconByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.icon : EmailIcon
}

const getPlaceholderByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.placeholder : __( 'Enter content' )
}

const getTitleByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.title : __( 'Enter content' )
}

const getMetaKeyByVitalType = ( name ) => {
	const variation = find( variations, { name } )
	return variation ? variation.metaKey : ''
}

const setVitalValue = ( vitalType, value ) => {
	const metaKey = getMetaKeyByVitalType( vitalType )

	let meta = {}
	meta[ metaKey ] = value

	dispatch( 'core/editor' ).editPost( {
		meta
	} )
}

/**
 * Edit function.
 *
 * @return {WPElement} Element to render.
 */
export default function edit( {
	attributes,
	isSelected,
	setAttributes,
} ) {
	const {
		vitalType
	} = attributes

	const blockProps = () => {
		let classNames = [ 'ramp-profile-vital-link' ]

		return useBlockProps( {
			className: classNames
		} )
	}

	const { value } = useSelect( ( select ) => {
		const postMeta = select( 'core/editor' ).getEditedPostAttribute( 'meta' )

		if ( postMeta ) {
			const metaKey = getMetaKeyByVitalType( vitalType )

			return {
				value: postMeta[ metaKey ]
			}
		} else {
			return {
				value: ''
			}
		}
	}, [] )

	const IconComponent = getIconByVitalType( vitalType )

	const ref = useRef();

	const notSelectedText = !! value ? value : getTitleByVitalType( vitalType )

	let notSelectedClassNames = 'ramp-profile-vital-link-text'
	if ( ! value ) {
		notSelectedClassNames += ' has-placeholder'
	}

	return (
		<div { ...blockProps() }>
			<IconComponent />

			{ isSelected && (
				<div className="profile-vital-link-edit">
					<TextControl
						label={ getTitleByVitalType( vitalType ) }
						value={ value }
						onChange={ ( value ) => setVitalValue( vitalType, value ) }
						placeholder={ getPlaceholderByVitalType( vitalType ) }
					/>
				</div>
			) }

			{ ! isSelected && (
				<Button ref={ref}>
					<span className={notSelectedClassNames}>
						{notSelectedText}
					</span>
				</Button>
			) }
		</div>
	)
}
