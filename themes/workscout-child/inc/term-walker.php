<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 25.10.17
 * Time: 15:40
 * To change this template use File | Settings | File Templates.
 */
function get_Walker_Category_Checklist_Custom() {


    class Walker_Category_Checklist_Custom extends Walker_Category_Checklist {
        public function start_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent<ul class='children'>\n";
        }


        public function end_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }

        public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
            if ( empty( $args['taxonomy'] ) ) {
                $taxonomy = 'category';
            } else {
                $taxonomy = $args['taxonomy'];
            }

            if ( $taxonomy == 'category' ) {
                $name = 'post_category';
            } else {
                $name = 'tax_input[' . $taxonomy . ']';
            }

            $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
            $class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

            $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

            if ( ! empty( $args['list_only'] ) ) {
                $aria_cheched = 'false';
                $inner_class = 'category';

                if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
                    $inner_class .= ' selected';
                    $aria_cheched = 'true';
                }

                /** This filter is documented in wp-includes/category-template.php */
                $output .= "\n" . '<li' . $class . '>' .
                    '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                    ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
                    esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
            } else {
                /** This filter is documented in wp-includes/category-template.php */
                $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                    '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                    checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                    disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
                    '<span>'.esc_html( apply_filters( 'the_category', $category->name ) ) . '</span></label>';
            }
        }

        public function end_el( &$output, $category, $depth = 0, $args = array() ) {
            $output .= "</li>\n";
        }

    }
    return new Walker_Category_Checklist_Custom;
}