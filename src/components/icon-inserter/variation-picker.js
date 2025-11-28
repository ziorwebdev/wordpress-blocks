/**
 * WordPress dependencies
 */
const { Dropdown, ToolbarButton, MenuGroup, MenuItem } = wp.components;
const { BlockControls } = wp.blockEditor;
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import variations from '../../blocks/icon/variations';

const ToolbarVariationPicker = ( { attributes, setAttributes } ) => {
	const { service } = attributes;

	// find current variation
	const current = variations.find(
		(v) => v.attributes.service === service
	) || variations[0];
    console.log("variations", variations);
	return (
		<BlockControls>
			<Dropdown
				popoverProps={{ placement: 'bottom-start' }}
				renderToggle={ ( { isOpen, onToggle } ) => (
					<ToolbarButton
						icon={ current.icon }
						label={ __( 'Change icon' ) }
						showTooltip
						onClick={ onToggle }
						aria-expanded={ isOpen }
					/>
				) }
				renderContent={ () => (
					<MenuGroup>
						{ variations.map( ( variation ) => (
							<MenuItem
								key={ variation.name }
								icon={ variation.icon }
								isSelected={ variation.attributes.service === service }
								onClick={ () => {
									setAttributes({
										service: variation.attributes.service,
									});
								}}
							>
								{ variation.title }
							</MenuItem>
						) ) }
					</MenuGroup>
				) }
			/>
		</BlockControls>
	);
};

export default ToolbarVariationPicker;
