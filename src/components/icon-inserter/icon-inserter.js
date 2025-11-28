/**
 * WordPress dependencies
 */
const { useSelect, useDispatch } = wp.data;
const { createInterpolateElement, Fragment } = wp.element;
const {
	BlockControls,
} = wp.blockEditor;
const {
	Dropdown,
	ToolbarButton,
	Icon,
	__experimentalMenuGroup: MenuGroup,
	__experimentalMenuItem: MenuItem,
} = wp.components;
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import variations from '../../blocks/icon/variations'; // path to your variations.js

const ToolbarBlockInserter = ( { clientId, attributes, setAttributes } ) => {
	// const { service } = attributes;
	console.log("attributes", attributes);
	// const { updateBlockAttributes } = useDispatch( 'core/block-editor' );

	// // Current variation object
	// const currentVariation = variations.find(
	// 	( v ) => v.attributes.service === service
	// ) || variations[ 0 ];

	return (
		<BlockControls>
			<Dropdown
				popoverProps={ { placement: 'bottom-start' } }
				renderToggle={ ( { isOpen, onToggle } ) => (
					<ToolbarButton
						// icon={ currentVariation.icon }
						label={ __( 'Change icon' ) }
						onClick={ onToggle }
						aria-expanded={ isOpen }
						aria-haspopup="true"
					/>
				) }
				renderContent={ () => (
					<div style={ { maxHeight: '300px', overflowY: 'auto' } }>
						<MenuGroup>
							{ variations.map( ( variation ) => (
								<MenuItem
									key={ variation.name }
									icon={ variation.icon }
									onClick={ () => {
										setAttributes( {
											service: variation.attributes.service,
										} );
									} }
									isSelected={
										false
									}
								>
									{ variation.title }
								</MenuItem>
							) ) }
						</MenuGroup>
					</div>
				) }
			/>
		</BlockControls>
	);
};

export default ToolbarBlockInserter;
