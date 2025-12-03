<?php
/**
 * Blocks class
 *
 * @package ZiorWebDev\WordPressBlocks
 * @since 1.0.0
 */
namespace ZiorWebDev\WordPressBlocks;

/**
 * Class Blocks
 *
 * @package ZiorWebDev\WordPressBlocks
 * @since 1.0.0
 */
final class Blocks {

	/**
	 * Singleton instance of the Plugin class.
	 *
	 * @var Load
	 */
	protected static $instance;

	/**
	 * Class constructor.
	 * 
	 */
	public function __construct() {
		add_filter( 'render_block_context', array( $this, 'inject_parent_context' ), 10, 3 );
		error_log( 'Blocks constructor' );
		Blocks\Icon\Icon::get_instance();
		// Icon Picker is static block, it does not need to be instantiated in PHP.
		// Blocks\IconPicker\IconPicker::get_instance();
		Blocks\IconList\IconList::get_instance();
	}

	/**
	 * Inject parent icon-picker attributes into child icon context.
	 *
	 * @param array $context      The current block context.
	 * @param array $parsed_block The parsed block array.
	 * @param object $parent_block The parent block object.
	 *
	 * @return array Modified block context.
	 */
	public function inject_parent_context( $context, $parsed_block, $parent_block ) {
		// Only apply to the child block.
		if ( ! isset( $parsed_block['blockName'] ) || 'ziorwebdev/icon' !== $parsed_block['blockName'] ) {
			return $context;
		}

		// Ensure parent exists and is icon-picker.
		if ( ! isset( $parent_block->parsed_block['blockName'] ) 
			|| 'ziorwebdev/icon-picker' !== $parent_block->parsed_block['blockName'] ) {
			return $context;
		}

		$parent_attrs = $parent_block->parsed_block['attrs'] ?? [];

		// Pass parent attributes into child context.
		foreach ( [ 'iconColorValue', 'iconBackgroundColorValue', 'showLabels', 'size' ] as $key ) {
			if ( isset( $parent_attrs[ $key ] ) ) {
				$context[ $key ] = $parent_attrs[ $key ];
			}
		}

		return $context;
	}

	/**
	 * Returns instance of Settings.
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
