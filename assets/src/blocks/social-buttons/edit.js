import './editor.scss'

import { __ } from '@wordpress/i18n'

import { ToggleControl } from '@wordpress/components'

import { __experimentalNumberControl as NumberControl } from '@wordpress/components';

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor'

import { store as coreStore } from '@wordpress/core-data'
import { store as postStore } from '@wordpress/editor'

import { useSelect } from '@wordpress/data'

import {
	PanelBody,
	PanelRow
} from '@wordpress/components'

import PublicationDateToggle from '../../components/PublicationDateToggle'

export default function edit( {
	clientId,
	context: { postType, postId },
	attributes,
	setAttributes
} ) {
	const {
		altmetricsEnabled,
		altmetricsThreshold,
		headingText,
		showPublicationDate
	} = attributes

	const blockProps = useBlockProps({
		className: [ 'sidebar-section' ]
	})

	const headingTextValue = headingText ?? __( 'Version', 'ramp' )

	const { dkpdfIsEnabled } = RAMPBlocks

	const thresholdDescriptionId = 'threshold-description- ' + clientId

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Altmetrics Settings', 'ramp' ) }
				>
					<PanelRow>
						<ToggleControl
							label={ __( 'Enable Altmetrics?', 'ramp' ) }
							help={ __( 'Almetrics badges will appear only on those posts that have a DOI.', 'ramp' ) }
							checked={ altmetricsEnabled }
							onChange={ ( altmetricsEnabled ) => setAttributes( { altmetricsEnabled } ) }
						/>
					</PanelRow>

					<PanelRow>
						<NumberControl
							label={ __( 'Display Threshold', 'ramp' ) }
							value={ altmetricsThreshold }
							min={ 1 }
							step={ 1 }
							onChange={ ( altmetricsThreshold ) => setAttributes( { altmetricsThreshold } ) }
						/>
					</PanelRow>

					<PanelRow>
						<p className="description">
							{ __( 'Items with an Altmetrics score lower than this threshold will not display a badge.', 'ramp' ) }
						</p>
					</PanelRow>

				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="social-buttons-links">
					<span className="social-button social-button-facebook">
						<span className="screen-reader-text">{ __( 'Send to Facebook', 'ramp' ) }</span>
					</span>

					<span className="social-button social-button-twitter">
						<span className="screen-reader-text">{ __( 'Send to Twitter', 'ramp' ) }</span>
					</span>

					{ dkpdfIsEnabled && (
						<span className="social-button social-button-download">
							<span className="screen-reader-text">{ __( 'Download as PDF', 'ramp' ) }</span>
						</span>
					) }
				</div>

				<span className="altmetrics-wrapper">
					{ altmetricsEnabled && (
						<div className="altmetrics-badge-placeholder"></div>
					) }
				</span>
			</div>
		</>
	)
}
