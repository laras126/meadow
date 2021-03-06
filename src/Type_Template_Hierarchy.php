<?php
namespace Rarst\Meadow;

/**
 * Prepends template hierarchy with Twig versions of templates.
 */
class Type_Template_Hierarchy {

	/** @var string[] $template_types Template type names to be used for dynamic hooks. */
	public $template_types = array(
		'embed',
		'404',
		'search',
		'taxonomy',
		'frontpage',
		'home',
		'attachment',
		'single',
		'page',
		'singular',
		'category',
		'tag',
		'author',
		'date',
		'archive',
		'commentspopup',
		'paged',
		'index',
	);

	/** @var string $type Keep track of last processed template type. */
	protected $type = '';

	public function enable() {

		add_filter( 'template_include', array( $this, 'template_include' ), 9 );

		foreach ( $this->template_types as $type ) {
			add_filter( "{$type}_template_hierarchy", array( $this, 'template_hierarchy' ) );
		}
	}

	public function disable() {

		remove_filter( 'template_include', array( $this, 'template_include' ), 9 );

		foreach ( $this->template_types as $type ) {
			remove_filter( "{$type}_template_hierarchy", array( $this, 'template_hierarchy' ) );
		}
	}

	/**
	 * @param string[] $templates Array of possible PHP templates, generated by WP core.
	 *
	 * @return string[] Array of templates, prepended with Twig versions.
	 */
	public function template_hierarchy( $templates ) {

		$this->type = substr( current_filter(), 0, - 19 ); // Trim '_template_hierarchy' from end.

		$twig_templates = [];

		foreach ( $templates as $php_template ) {

			if ( '.php' === substr( $php_template, \strlen( $php_template ) - 4 ) ) {

				$twig_templates[] = substr( $php_template, 0, - 4 ) . '.twig';
			}
		}

		return array_merge( $twig_templates, $templates );
	}

	/**
	 * @param string $template Template located by loader after going through hierarchy.
	 *
	 * @return string
	 */
	public function template_include( $template ) {

		return apply_filters( 'meadow_query_template', $template, $this->type );
	}
}
