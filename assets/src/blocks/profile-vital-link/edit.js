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

/**
 * Editor styles.
 */
import './editor.scss';

import {
	getMetaKeyByVitalType,
	getPlaceholderByVitalType,
	getTitleByVitalType,
} from './variation-utils'

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
		value,
		vitalType
	} = attributes

	const blockProps = () => {
		return useBlockProps( {
			className: [
				'ramp-profile-vital-link-' + vitalType
			]
		} )
	}

	const ref = useRef();

	const notSelectedText = !! value ? value : getTitleByVitalType( vitalType )

	let notSelectedClassNames = 'ramp-profile-vital-link-text'
	if ( ! value ) {
		notSelectedClassNames += ' has-placeholder'
	}

	return (
		<div { ...blockProps() }>
			{ isSelected && (
				<div className="profile-vital-link-edit">
					<TextControl
						hideLabelFromVision={ true }
						label={ getTitleByVitalType( vitalType ) }
						value={ value }
						onChange={ ( value ) => setAttributes( { value } ) }
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
