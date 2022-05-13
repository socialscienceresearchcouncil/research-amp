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

	const headingTextValue = headingText ?? __( 'Version', 'research-amp' )

	const { dkpdfIsEnabled } = RAMPBlocks

	const thresholdDescriptionId = 'threshold-description- ' + clientId

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Altmetrics Settings', 'research-amp' ) }
				>
					<PanelRow>
						<ToggleControl
							label={ __( 'Enable Altmetrics?', 'research-amp' ) }
							help={ __( 'Almetrics badges will appear only on those posts that have a DOI.', 'research-amp' ) }
							checked={ altmetricsEnabled }
							onChange={ ( altmetricsEnabled ) => setAttributes( { altmetricsEnabled } ) }
						/>
					</PanelRow>

					<PanelRow>
						<NumberControl
							label={ __( 'Display Threshold', 'research-amp' ) }
							value={ altmetricsThreshold }
							min={ 1 }
							step={ 1 }
							onChange={ ( altmetricsThreshold ) => setAttributes( { altmetricsThreshold } ) }
						/>
					</PanelRow>

					<PanelRow>
						<p className="description">
							{ __( 'Items with an Altmetrics score lower than this threshold will not display a badge.', 'research-amp' ) }
						</p>
					</PanelRow>

				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="social-buttons-links">
					<span className="social-button social-button-facebook">
						<span className="screen-reader-text">{ __( 'Send to Facebook', 'research-amp' ) }</span>
					</span>

					<span className="social-button social-button-twitter">
						<span className="screen-reader-text">{ __( 'Send to Twitter', 'research-amp' ) }</span>
					</span>

					{ dkpdfIsEnabled && (
						<span className="social-button social-button-download">
							<span className="screen-reader-text">{ __( 'Download as PDF', 'research-amp' ) }</span>
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
