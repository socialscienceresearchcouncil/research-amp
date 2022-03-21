import { __ } from '@wordpress/i18n'

import { useSelect } from '@wordpress/data'

import './content-mode-panel.scss'

import ResearchTopicSelector from './ResearchTopicSelector'
import ProfileSelector from './ProfileSelector'

const ContentModeControl = ( props ) => {
	const {
		changeCallback,
		changeProfileIdCallback,
		changeResearchTopicIdCallback,
		legend,
		selectedMode,
		selectedProfileId,
		selectedResearchTopicId
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

	return (
		<>
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
						selected={ selectedResearchTopicId }
						onChangeCallback={ changeResearchTopicIdCallback }
					/>

					<ProfileSelector
						onChangeCallback={ changeProfileIdCallback }
						selectedProfileId={ selectedProfileId }
					/>

				</fieldset>
			) }
		</>
	)
}

export default ContentModeControl
