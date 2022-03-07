import { __ } from '@wordpress/i18n'
import { useSelect } from '@wordpress/data'
import { SelectControl } from '@wordpress/components'

const ResearchTopicSelector = ( props ) => {
	const {
		label,
		onChangeCallback,
		selected
	} = props

	const { researchTopics } = useSelect( ( select ) => {
		const researchTopics = select( 'ramp' ).getResearchTopics()

		return {
			researchTopics
		}
	} )

	let researchTopicsOptions = researchTopics.map( ( topic ) => {
		return {
			label: topic.title.rendered,
			value: topic.id.toString()
		}
	} )

	researchTopicsOptions.unshift( { label: __( 'Select a Research Topic', 'ramp' ), value: '' } )

	return (
		<SelectControl
			label={ label }
			value={ selected  }
			options={ researchTopicsOptions }
			onChange={ ( researchTopic ) => onChangeCallback( researchTopic ) }
		/>
	)
}

export default ResearchTopicSelector
