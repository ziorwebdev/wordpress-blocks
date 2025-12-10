<?php
/**
 * Server-side rendering of the `ziorwebdev/icon` blocks.
 *
 * @package ZiorWebDev\WordPressBlocks
 */

namespace ZiorWebDev\WordPressBlocks\Blocks\Icon;

use ZiorWebDev\WordPressBlocks\Blocks;

/**
 * Icon Picker class
 *
 * @package ZiorWebDev\WordPressBlocks
 * @since 1.0.0
 */
class Block extends Blocks\Base {

	/**
	 * Block name
	 *
	 * @var $block_name
	 */
	protected $block_name = 'ziorwebdev/icon';

	/**
	 * Path of the block.json file
	 *
	 * @var $block_json
	 */
	protected $block_json = __DIR__ . '/block.json';

	/**
	 * Singleton instance of the Plugin class.
	 *
	 * @var Icon
	 */
	protected static $instance;

	/**
	 * Convert string to title case
	 *
	 * @param String $title
	 * @return string
	 */
	private function convert_title_case( string $title ): string {
		$title = preg_replace( '/[^a-zA-Z0-9]+/', ' ', $title );
		$title = trim( $title );
		$title = preg_replace( '/\s+/', ' ', $title );
		$title = ucwords( strtolower( $title ) );

		return $title;
	}

	/**
	 * Renders the `ziorwebdev/icon` block on server.
	 *
	 * @since 1.0.0
	 * @param Array    $attributes The block attributes.
	 * @param String   $content    InnerBlocks content of the Block.
	 * @param WP_Block $block      Block object.
	 * @return string Rendered HTML of the referenced block.
	 */
	public function render( $attributes, $content, $block ) {
		$open_in_new_tab = isset( $block->context['openInNewTab'] ) ? $block->context['openInNewTab'] : false;
		$text            = ! empty( $attributes['label'] ) ? trim( $attributes['label'] ) : '';
		$service         = isset( $attributes['service'] ) ? $attributes['service'] : 'Icon';
		$url             = isset( $block->context['iconUrl'] ) ? $block->context['iconUrl'] : false;
		$text            = $text ? $text : $this->get_name( $service );
		$rel             = isset( $attributes['rel'] ) ? $attributes['rel'] : '';
		$show_labels     = array_key_exists( 'showLabels', $block->context ) ? $block->context['showLabels'] : false;

		/**
		 * Prepend emails with `mailto:` if not set.
		 * The `is_email` returns false for emails with schema.
		 */
		if ( is_email( $url ) ) {
			$url = 'mailto:' . antispambot( $url );
		}

		/**
		 * Prepend URL with https:// if it doesn't appear to contain a scheme
		 * and it's not a relative link or a fragment.
		 */
		if ( ! empty( $url ) && ! parse_url( $url, PHP_URL_SCHEME ) && ! str_starts_with( $url, '//' ) && ! str_starts_with( $url, '#' ) ) {
			$url = 'https://' . $url;
		}

		$icon               = $this->get_icon( $service );
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'wp-ziorwebdev-icon wp-ziorwebdev-icon-' . $service . $this->get_color_classes( $block->context ),
				'style' => $this->get_color_styles( $block->context ),
			)
		);

		$content = '<span ' . $wrapper_attributes . '>';

		/**
		 * TODO: Fix the styling of the icon even without the achor tag.
		 */
		$content .= '<a ';

		if ( ! empty( $url ) ) {
			$content .= ' href="' . esc_url( $url ) . '"';
		}

		$content .= 'class="wp-block-ziorwebdev-icon-anchor">';
		$content .= $icon;
		$content .= '<span class="wp-block-ziorwebdev-icon-label' . ( $show_labels ? '' : ' screen-reader-text' ) . '">' . esc_html( $text ) . '</span>';
		$content .= '</a></span>';

		$processor = new \WP_HTML_Tag_Processor( $content );
		$processor->next_tag( 'a' );

		if ( $open_in_new_tab ) {
			$processor->set_attribute( 'rel', trim( $rel . ' noopener nofollow' ) );
			$processor->set_attribute( 'target', '_blank' );
		} elseif ( '' !== $rel ) {
			$processor->set_attribute( 'rel', trim( $rel ) );
		}

		$html = $processor->get_updated_html();

		return $html;
	}

	/**
	 * Returns the SVG for icon.
	 *
	 * @since 1.0.0
	 * @param string $service The service icon.
	 * @return string SVG Element for service icon.
	 */
	public function get_icon( $service ) {
		$services = block_core_social_link_services();

		if ( isset( $services[ $service ] ) && isset( $services[ $service ]['icon'] ) ) {
			return $services[ $service ]['icon'];
		}

		/**
		 * Fallback: return Dashicon markup if service icon does not exist
		 * Example: <span class="dashicons dashicons-share"></span>
		 */
		return sprintf(
			'<span class="dashicons dashicons-%s"></span>',
			esc_attr( $service )
		);
	}

	/**
	 * Returns the brand name for icon.
	 *
	 * @since 1.0.0
	 * @param string $service The service icon.
	 * @return string Brand label.
	 */
	public function get_name( $service ) {
		$services = block_core_social_link_services();

		if ( isset( $services[ $service ] ) && isset( $services[ $service ]['name'] ) ) {
			return $services[ $service ]['name'];
		}

		/**
		 * Convert the service to title case and return.
		 */
		return $this->convert_title_case( $service );
	}

	/**
	 * Returns CSS styles for icon and icon background colors.
	 *
	 * @since 1.0.0
	 * @param array $context Block context passed to icon.
	 * @return string Inline CSS styles for link's icon and background colors.
	 */
	public function get_color_styles( $context ) {
		$styles = array();

		if ( array_key_exists( 'iconColorValue', $context ) ) {
			$styles[] = 'color:' . $context['iconColorValue'] . ';';
		}

		if ( array_key_exists( 'iconBackgroundColorValue', $context ) ) {
			$styles[] = 'background-color:' . $context['iconBackgroundColorValue'] . ';';
		}

		return implode( '', $styles );
	}

	/**
	 * Returns CSS classes for icon and icon background colors.
	 *
	 * @since 1.0.0
	 * @param array $context Block context passed to icon.
	 * @return string CSS classes for link's icon and background colors.
	 */
	public function get_color_classes( $context ) {
		$classes = array();

		if ( array_key_exists( 'iconColor', $context ) ) {
			$classes[] = 'has-' . $context['iconColor'] . '-color';
		}

		if ( array_key_exists( 'iconBackgroundColor', $context ) ) {
			$classes[] = 'has-' . $context['iconBackgroundColor'] . '-background-color';
		}

		return ' ' . implode( ' ', $classes );
	}

	/**
	 * Inject parent icon-picker attributes into child icon context.
	 *
	 * @param array  $context      The current block context.
	 * @param array  $parsed_block The parsed block array.
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

		$parent_attrs = $parent_block->parsed_block['attrs'] ?? array();

		// Pass parent attributes into child context.
		foreach ( array( 'iconUrl', 'iconColorValue', 'iconBackgroundColorValue', 'showLabels', 'size', 'openInNewTab' ) as $key ) {
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
