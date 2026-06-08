<?php
/**
 * Walkers cho menu chính: desktop (mega) + mobile (accordion).
 *
 * Mục mega: thêm CSS class `annam-mega-tour` cho menu item trong Giao diện → Menu
 * (bật "CSS Classes" trong Screen Options). Desktop: panel mega danh mục WC;
 * mobile: các mục con trong admin hiển thị accordion.
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Desktop: item có class `annam-mega-tour` dùng nút + panel mega, không render submenu trong DOM desktop.
 */
class Annam_Primary_Menu_Walker_Desktop extends Walker_Nav_Menu {

	/**
	 * @param WP_Post $element
	 */
	private function item_is_mega_tour( $element ) {
		return in_array( 'annam-mega-tour', (array) $element->classes, true );
	}

	/**
	 * @param WP_Post $element
	 * @param array   $children_elements
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( 0 === (int) $depth && $this->item_is_mega_tour( $element ) ) {
			$id           = $element->db_id;
			$saved        = isset( $children_elements[ $id ] ) ? $children_elements[ $id ] : null;
			$had_children = isset( $children_elements[ $id ] );
			if ( $had_children ) {
				unset( $children_elements[ $id ] );
			}
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
			if ( $had_children && null !== $saved ) {
				$children_elements[ $id ] = $saved;
			}
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * @param WP_Post $data_object
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		if ( 0 === (int) $depth && $this->item_is_mega_tour( $data_object ) ) {
			$this->render_mega_parent_start( $output, $data_object, $args );
			return;
		}
		parent::start_el( $output, $data_object, $depth, $args, $current_object_id );
	}

	/**
	 * @param WP_Post $data_object
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		if ( 0 === (int) $depth && $this->item_is_mega_tour( $data_object ) ) {
			if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$n = '';
			} else {
				$n = "\n";
			}
			$panel_id = 'annam-mega-panel-' . (int) $data_object->ID;
			ob_start();
			annam_site_header_render_mega_panel( $panel_id );
			$output .= ob_get_clean();
			$output .= "</li>{$n}";
			return;
		}
		parent::end_el( $output, $data_object, $depth, $args );
	}

	/**
	 * @param string  $output
	 * @param WP_Post $menu_item
	 * @param object  $args
	 */
	private function render_mega_parent_start( &$output, $menu_item, $args ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = '';

		$classes   = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;
		$classes[] = 'menu-item-' . $menu_item->ID;

		$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, 0 );

		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, 0 ) );
		$id          = apply_filters( 'nav_menu_item_id', 'menu-item-' . $menu_item->ID, $menu_item, $args, 0 );

		$li_atts          = array();
		$li_atts['id']    = ! empty( $id ) ? $id : '';
		$li_atts['class'] = ! empty( $class_names ) ? $class_names : '';
		$li_atts          = apply_filters( 'nav_menu_item_attributes', $li_atts, $menu_item, $args, 0 );
		$li_attributes    = $this->build_atts( $li_atts );

		$output .= $indent . '<li' . $li_attributes . '>';

		$title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, 0 );

		$panel_id  = 'annam-mega-panel-' . (int) $menu_item->ID;
		$trigger_id = 'annam-mega-trigger-' . (int) $menu_item->ID;

		$btn_atts = array(
			'type'          => 'button',
			'class'         => 'annam-site-header__mega-trigger annam-site-header__mega-trigger--btn',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
			'aria-controls' => $panel_id,
			'id'            => $trigger_id,
		);
		$btn_atts = apply_filters( 'annam_mega_tour_trigger_attributes', $btn_atts, $menu_item, $args );
		$btn_html = '<button' . $this->build_atts( $btn_atts ) . '>';
		$btn_html .= $args->link_before . esc_html( wp_strip_all_tags( $title ) ) . $args->link_after;
		$btn_html .= '</button>';

		$item_output  = $args->before;
		$item_output .= $btn_html;
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, 0, $args );
	}
}

/**
 * Mobile drawer: hàng có con → nút mở accordion + submenu.
 */
class Annam_Primary_Menu_Walker_Mobile extends Walker_Nav_Menu {

	/**
	 * @var string
	 */
	protected $pending_submenu_id = '';

	/**
	 * @param string   $output
	 * @param int      $depth
	 * @param stdClass $args
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		$sub_id = $this->pending_submenu_id;
		$this->pending_submenu_id = '';

		$classes = array( 'annam-site-header__drawer-sub', 'sub-menu' );
		/**
		 * @param string[] $classes
		 */
		$class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );

		$atts          = array();
		$atts['class'] = $class_names;
		if ( $sub_id ) {
			$atts['id']            = $sub_id;
			$atts['hidden']        = 'hidden';
			$atts['aria-hidden']   = 'true';
		}
		$atts       = apply_filters( 'nav_menu_submenu_attributes', $atts, $args, $depth );
		$attributes = $this->build_atts( $atts );

		$output .= "{$n}{$indent}<ul{$attributes}>{$n}";
	}

	/**
	 * @param WP_Post $data_object
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$menu_item = $data_object;

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;
		$classes[] = 'menu-item-' . $menu_item->ID;

		$args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth );

		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, $depth ) );

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $menu_item->ID, $menu_item, $args, $depth );

		$li_atts          = array();
		$li_atts['id']    = ! empty( $id ) ? $id : '';
		$li_atts['class'] = ! empty( $class_names ) ? $class_names : '';
		$li_atts          = apply_filters( 'nav_menu_item_attributes', $li_atts, $menu_item, $args, $depth );
		$li_attributes    = $this->build_atts( $li_atts );

		$has_children = in_array( 'menu-item-has-children', $classes, true );
		if ( $has_children ) {
			$this->pending_submenu_id = 'annam-drawer-sub-' . (int) $menu_item->ID;
		} else {
			$this->pending_submenu_id = '';
		}

		$output .= $indent . '<li' . $li_attributes . '>';

		$title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, $depth );

		$atts           = array();
		$atts['target'] = ! empty( $menu_item->target ) ? $menu_item->target : '';
		$atts['rel']    = ! empty( $menu_item->xfn ) ? $menu_item->xfn : '';

		if ( ! empty( $menu_item->url ) ) {
			$atts['href'] = $menu_item->url;
		} else {
			$atts['href'] = '';
		}

		$atts['aria-current'] = $menu_item->current ? 'page' : '';

		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $menu_item, $args, $depth );
		$attributes = $this->build_atts( $atts );

		if ( $has_children ) {
			$output .= '<div class="annam-site-header__drawer-row">';
		}

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';

		if ( $has_children ) {
			$sub_id   = $this->pending_submenu_id;
			$label    = sprintf(
				/* translators: %s: menu item title */
				__( 'Mở mục con: %s', 'generatepress_child' ),
				wp_strip_all_tags( $title )
			);
			$item_output .= '<button type="button" class="annam-site-header__drawer-toggle" aria-expanded="false" aria-controls="' . esc_attr( $sub_id ) . '" aria-label="' . esc_attr( $label ) . '">';
			$item_output .= '<span class="annam-site-header__drawer-toggle-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></span>';
			$item_output .= '</button>';
			$item_output .= '</div>';
		}

		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args );
	}
}
