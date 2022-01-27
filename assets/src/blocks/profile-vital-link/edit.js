import { __ } from '@wordpress/i18n';

import {
	URLInput,
	URLPopover,
	useBlockProps
} from '@wordpress/block-editor';

import { useSelect } from '@wordpress/data';

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
	const variation = find( variations, { name } );
	return variation ? variation.icon : EmailIcon;
};

const getTitleByVitalType = ( name ) => {
	const variation = find( variations, { name } );
	return variation ? variation.title : __( 'Enter content' );
};

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
		let classNames = [ 'ramp-profile-vital-link' ]

		return useBlockProps( {
			className: classNames
		} )
	}

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
				<TextControl
					label={ getTitleByVitalType( name ) }
					value={ value }
					onChange={ ( value ) => setAttributes( { value } ) }
				/>
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
