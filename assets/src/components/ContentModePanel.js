import { __ } from '@wordpress/i18n'

import {
	Panel,
	PanelBody
} from '@wordpress/components'

import { useSelect } from '@wordpress/data'

import './content-mode-panel.scss'

import ResearchTopicSelector from './ResearchTopicSelector'
import { PostPicker } from './PostPicker'

const ContentModePanel = ( props ) => {
	const {
		changeCallback,
		changeProfileCallback,
		changeResearchTopicCallback,
		legend,
		selectedMode,
		selectedProfile,
		selectedResearchTopic
	} = props

	const changeCallbacks = {
		auto: () => {
			changeCallback( 'auto' )
		},
		all: () => {
			changeCallback( 'all' )
		},
		advanced: () => {
			changeCallback( 'advanced' )
		}
	}

	const contentModeOpts = [
		{
			'value': 'auto',
			'label': __( 'Use current context', 'ramp' ),
			'gloss': __( 'Show items relevant to the current context. When viewing a Profile or Research Topic, items will be shown only if linked to that Profile or Research Topic.', 'ramp' )
		},
		{
			'value': 'all',
			'label': __( 'All content', 'ramp' ),
			'gloss': __( 'Pull from all items, regardless of current page context.', 'ramp' )
		},
		{
			'value': 'advanced',
			'label': __( 'Advanced', 'ramp' ),
			'gloss': __( 'Advanced configuration options', 'ramp' )
		}
	]

	const { profile } = useSelect( ( select ) => {
		let profile = {}
		if ( selectedProfile ) {
			profile = select( 'ramp' ).getPost( selectedProfile, 'ramp_profile' )
		}

		return {
			profile
		}
	}, [ selectedProfile ] )

	return (
		<Panel>
			<PanelBody
				title={ __( 'Content', 'ramp' ) }
			>
				<fieldset
					key="content-mode-selector"
					className="content-mode-selector"
				>
					<legend>{ legend }</legend>
					{ contentModeOpts.map( ( { value, label, gloss } ) => (
						<div
						  key={ value }
							className="content-mode-selector__choice"
						>
							<input
								type="radio"
								name="content-mode"
								value={ value }
								id={ `content-mode-${ value }` }
								aria-describedby={ `content-mode-${ value }-description` }
								onChange={ changeCallbacks[ value ] }
								checked={ value === selectedMode }
							/>

							<label
								htmlFor={ `content-mode-${ value }` }
							>
								{ label }
							</label>

							<p
								id={ `content-mode-${ value }-description` }
								className="content-mode-description"
							>
								{ gloss }
							</p>
						</div>
					) ) }
				</fieldset>

				{ 'advanced' === selectedMode && (
					<fieldset
						className="content-mode-selector-advanced-options"
						key="content-mode-selector-advanced-options"
					>
						<legend>{ __( 'Limit displayed items to those associated with a specific Research Topic or Profile.', 'ramp' ) }</legend>

						<ResearchTopicSelector
							label={ __( 'Research Topic', 'ramp' ) }
							selected={ selectedResearchTopic }
							onChangeCallback={ changeResearchTopicCallback }
						/>

						<PostPicker
							postTypes={ [ 'ramp_profile' ] }
							onSelectPost={ changeProfileCallback }
							label={ __( 'Profile', 'ramp' ) }
							placeholder={ __( 'Start typing to search.', 'ramp' ) }
						/>
					</fieldset>
				) }
			</PanelBody>
		</Panel>
	)
}

export default ContentModePanel
