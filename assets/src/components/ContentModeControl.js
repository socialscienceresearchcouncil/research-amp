import { __ } from '@wordpress/i18n';

import { PanelRow } from '@wordpress/components';

import { useSelect } from '@wordpress/data';

import './content-mode-panel.scss';

import ResearchTopicSelector from './ResearchTopicSelector';
import ProfileSelector from './ProfileSelector';

const ContentModeControl = ( props ) => {
	const {
		changeCallback,
		changeProfileIdCallback,
		changeResearchTopicIdCallback,
		disabledTypes,
		glossAdvanced,
		glossAll,
		glossAuto,
		labelAdvanced,
		labelAll,
		labelAuto,
		legend,
		selectedMode,
		selectedProfileId,
		selectedResearchTopicId,
	} = props;

	const changeCallbacks = {
		auto: () => {
			changeCallback( 'auto' );
		},
		all: () => {
			changeCallback( 'all' );
		},
		advanced: () => {
			changeCallback( 'advanced' );
		},
	};

	const disabledItemTypes = {
		...{ profile: false, researchTopic: false },
		...disabledTypes,
	};

	const labels = {
		auto: labelAuto ?? __( 'Relevant Items', 'research-amp' ),
		all: labelAll ?? __( 'All Items', 'research-amp' ),
		advanced: labelAdvanced ?? __( 'Advanced', 'research-amp' ),
	};

	const glosses = {
		auto:
			glossAuto ??
			__(
				'Show items relevant to the current Research Topic or Profile context.',
				'research-amp'
			),
		all:
			glossAll ??
			__(
				'Items will be shown regardless of associated Research Topic or Profile',
				'research-amp'
			),
		advanced:
			glossAdvanced ??
			__(
				'Show items associated with a specific Research Topic or Profile',
				'research-amp'
			),
	};

	const contentModeOpts = [
		{
			value: 'auto',
			label: labels.auto,
			gloss: glosses.auto,
		},
		{
			value: 'all',
			label: labels.all,
			gloss: glosses.all,
		},
		{
			value: 'advanced',
			label: labels.advanced,
			gloss: glosses.advanced,
		},
	];

	return (
		<>
			<PanelRow>
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

							<label htmlFor={ `content-mode-${ value }` }>
								{ label }
							</label>

							{ value === selectedMode && (
								<p
									id={ `content-mode-${ value }-description` }
									className="content-mode-description"
								>
									{ gloss }
								</p>
							) }
						</div>
					) ) }
				</fieldset>
			</PanelRow>

			{ 'advanced' === selectedMode && (
				<PanelRow>
					<fieldset
						className="content-mode-selector-advanced-options"
						key="content-mode-selector-advanced-options"
					>
						<legend>{ __( 'Filtered by', 'research-amp' ) }</legend>

						{ ! disabledItemTypes.researchTopic && (
							<div className="option-row">
								<ResearchTopicSelector
									disabled={ !! selectedProfileId }
									label={ __(
										'Research Topic',
										'research-amp'
									) }
									selected={ selectedResearchTopicId }
									onChangeCallback={
										changeResearchTopicIdCallback
									}
								/>
							</div>
						) }

						{ ! disabledItemTypes.profile && (
							<div className="option-row">
								<ProfileSelector
									disabled={ !! selectedResearchTopicId }
									onChangeCallback={ changeProfileIdCallback }
									selectedProfileId={ selectedProfileId }
								/>
							</div>
						) }
					</fieldset>
				</PanelRow>
			) }
		</>
	);
};

export default ContentModeControl;
