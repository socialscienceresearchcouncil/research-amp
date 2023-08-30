import './fake-select.scss';

const FakeSelect = ( props ) => {
	const { text } = props;

	return (
		<div className="fake-select-container">
			<span className="select2-selection__rendered">{ text }</span>
			<span className="select2-selection__arrow" role="presentation">
				<b role="presentation"></b>
			</span>
		</div>
	);
};

export default FakeSelect;
