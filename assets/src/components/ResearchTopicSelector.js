import { __ } from '@wordpress/i18n'
import { useSelect } from '@wordpress/data'

import Select from 'react-select'

import { unescapeString } from './ReorderableFlatTermSelector/utils'

const ResearchTopicSelector = ( props ) => {
	const {
		disabled = false,
		label,
		onChangeCallback,
		selected
	} = props

	const { researchTopics } = useSelect( ( select ) => {
		const researchTopics = select( 'research-amp' ).getResearchTopics()

		return {
			researchTopics
		}
	} )

	let researchTopicsOptions = researchTopics.map( ( topic ) => {
		return {
			label: unescapeString( topic.title.rendered ),
			value: topic.id
		}
	} )

	const selectedOption = researchTopicsOptions.find( ( option ) => option.value === selected  )

	const handleChange = ( selected ) => {
		const newValue = selected ? selected.value : 0
		onChangeCallback( newValue )
	}

	return (
		<>
			<label
				className="screen-reader-text"
			>{ __( 'Select a Research Topic', 'research-amp' ) }</label>

			<Select
				isDisabled={ disabled }
				isClearable={ true }
				label={ label }
				menuPortalTarget={document.querySelector('body')}
				options={ researchTopicsOptions }
				onChange={ handleChange }
				placeholder={ __( 'Select a Research Topic', 'research-amp' ) }
				value={ selectedOption }
			/>
		</>
	)
}

export default ResearchTopicSelector
