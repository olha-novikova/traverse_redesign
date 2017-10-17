<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Dinah!' );
}

class WP_PUR {
	public $options;

	function __construct() {
		add_action( 'admin_menu', array( $this, 'post_meta_box' ) );
		add_action( 'save_post', array( $this,'save_post' ) );

		$this->options = get_option( 'pur_options' );

		add_filter( 'manage_posts_columns', array( $this, 'add_columns' ) );
		add_filter( 'manage_pages_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'custom_post_column' ), 10, 2 );
		add_action( 'manage_pages_custom_column', array( $this, 'custom_post_column' ), 10, 2 );

		// BuddyPress
		if ( defined( 'BP_PLUGIN_DIR' ) ) {
			add_action( 'bp_members_admin_user_metaboxes', array( $this, 'bp_user_meta_box' ) );
			add_action( 'bp_members_admin_update_user', array( $this, 'bp_user_save_metabox' ), 10, 2 );
		}
	}
	
	function post_type_enabled( $post_type ) {
		return in_array( $post_type, array_merge( ( is_array( $this->options['post_types'] ) ? $this->options['post_types'] : array() ), array( 'page', 'post' ) ) );
	}

	function get_meta_roles( $type ) {
		global $wp_roles;

		$pur_roles = get_post_meta( $post->ID, 'pur-available-roles' );
		$pur_roles = is_array( $pur_roles ) ? $pur_roles : array();
		$roles = $wp_roles->get_names();
		$tmp = array();

		foreach ( $pur_roles as $role ) {
			$tmp[] = isset( $roles[ $role ] ) ? $roles[ $role ] : $role;
		}
	}
	
	function custom_post_column( $field, $post_id = null ) {
		global $post, $wp_roles;

		$post_id = null == $post_id ? $post->ID : $post_id;
		
		if ( 'pur' == $field ) {
			if ( in_array( get_post_meta( $post->ID, 'pur_control', true ), array( '', 'allow' ) ) ) {
				$pur_roles = get_post_meta( $post->ID, 'pur-available-roles' );
				$pur_roles = is_array( $pur_roles ) ? $pur_roles : array();
				$roles = $wp_roles->get_names();
				$tmp = array();

				foreach ( $pur_roles as $role ) {
					$tmp[] = isset( $roles[ $role ] ) ? $roles[ $role ] : $role;
				}

				echo ( count( $tmp ) > 0 ) ? sprintf( '<span style="color: green;" class="pur-allow">%s</span> %s', __( 'Allow:', 'pur' ), implode( ', ', $tmp ) ) : '';
			} else if ( 'block' == get_post_meta( $post->ID, 'pur_control', true ) ) {
				$pur_roles = get_post_meta( $post->ID, 'pur-blocked-roles' );
				$pur_roles = is_array( $pur_roles ) ? $pur_roles : array();
				$roles = $wp_roles->get_names();
				$tmp = array();

				foreach ( $pur_roles as $role ) {
					$tmp[] = isset( $roles[ $role ] ) ? $roles[ $role ] : $role;
				}

				echo ( count( $tmp ) > 0 ) ? sprintf( '<span style="color:red;" class="pur-block">%s</span> %s', __( 'Block:', 'pur' ), implode( ', ', $tmp ) ) : '';
			} else {
				echo 'none';
			}

			echo '';
		}
	}
	
	public static function add_columns( $columns ) {
		$columns['pur'] = __( 'Access Control', 'pur' );
		
		return $columns; 
	}
	
	function post_meta_box(){
		if(isset($this->options['restricted_metabox']) && '1'==$this->options['restricted_metabox']){
			global $userdata;
			$allowed_metabox = isset($this->options['allowed_metabox']) && is_array($this->options['allowed_metabox']) ? $this->options['allowed_metabox'] : array();
			if(0==count(array_intersect($allowed_metabox,$userdata->roles))){
				return;//user does not gets Access Control Metabox.
			}			
		}
		add_meta_box( 'pur-postmeta', __('Access Control','pur'),	array( &$this, 'form_template' ), 'page', 'side', 'low');
		add_meta_box( 'pur-postmeta', __('Access Control','pur'),	array( &$this, 'form_template' ), 'post', 'side', 'low');
		if(!empty($this->options['post_types'])&&count($this->options['post_types'])>0){
			foreach($this->options['post_types'] as $post_type){
				add_meta_box( 'pur-postmeta', __('Access Control','pur'),	array( &$this, 'form_template' ), $post_type, 'side', 'low');
			}
		}
	}

	function bp_user_meta_box() {
		add_meta_box(
			'pur-usermeta',
			__( 'Access Control', 'pur' ),
			array( $this, 'form_template' ),
			get_current_screen()->id,
			'side',
			'low',
			array(
				'show_in_nav' => false
			)
		);
	}
	
	function save_post($post_id){
		if ( !wp_verify_nonce( @$_POST['pur-nonce'], 'pur-nonce' )) {
			return $post_id;
		}
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
		  if ( !current_user_can( 'edit_page', $post_id ) )
		    return $post_id;
		} else {
		  if ( !current_user_can( 'edit_post', $post_id ) )
		    return $post_id;
		}

		$pur_roles = isset($_POST['pur_roles'])&&is_array($_POST['pur_roles'])?$_POST['pur_roles']:array();
		delete_post_meta($post_id,'pur-available-roles');
		delete_post_meta($post_id,'pur-blocked-roles');
		if(!empty($pur_roles)){
			foreach($pur_roles as $role){
				if(isset($_POST['pur_control'])){
					if($_POST['pur_control']=='allow'){
						add_post_meta($post_id,'pur-available-roles',$role);
					}else if($_POST['pur_control']=='block'){
						add_post_meta($post_id,'pur-blocked-roles',$role);
					}
				}
			}
		}
		
		foreach( array('pur_redir_url','pur_control') as $field){
			if(isset($_POST[$field])){
				update_post_meta($post_id,$field,$_POST[$field]);		
			}
		}
		
		foreach( array('pur_show_in_nav') as $checkbox_field ){
			$value = isset($_POST[$checkbox_field])?$_POST[$checkbox_field]:'';
			update_post_meta($post_id,$checkbox_field,$value);
		}
	}

	function bp_user_save_metabox( $doaction, $user_id ) {
		if ( ! wp_verify_nonce( $_POST['pur-nonce'], 'pur-nonce' ) )
			return $user_id;
		
		if ( ! current_user_can( 'edit_user', $user_id ) )
			return $user_id;

		$pur_roles = ( isset( $_POST['pur_roles'] ) && is_array( $_POST['pur_roles'] ) ) ? $_POST['pur_roles'] : array();
		delete_user_meta( $user_id, 'pur-available-roles' );
		delete_user_meta( $user_id, 'pur-blocked-roles' );
		
		if ( ! empty( $pur_roles) ) {
			foreach ( $pur_roles as $role ) {
				if ( isset( $_POST['pur_control'] ) ) {
					if ( $_POST['pur_control'] == 'allow' ) {
						add_user_meta( $user_id, 'pur-available-roles', $role );
					} else if ( $_POST['pur_control'] == 'block' ) {
						add_user_meta( $user_id, 'pur-blocked-roles', $role );
					}
				}
			}
		}
		
		foreach ( array( 'pur_redir_url', 'pur_control' ) as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_user_meta( $user_id, $field, $_POST[ $field ] );		
			}
		}
	}

		
	function form_template( $post, $metabox ) {
		$item_id = $post->ID;
		$item_type = 'post';

		if ( $metabox['id'] == 'pur-usermeta' && defined( 'BP_PLUGIN_DIR' ) ) {
			$item_id = ( isset( $_GET['user_id'] ) ) ? absint( $_GET['user_id'] ) : get_current_user_id();
			$item_type = 'user';
		}
		
		echo '<input type="hidden" name="pur-nonce" id="pur-nonce" value="' . wp_create_nonce( 'pur-nonce' ) . '" />';
		
		$pur_control = trim( $this->get_item_meta( $item_type, $item_id, 'pur_control', true ) );
		$pur_control = ( '' == $pur_control ) ? 'allow' : $pur_control;
		
		if ( $pur_control == 'allow' ) {
			$pur_roles = $this->get_item_meta( $item_type, $item_id, 'pur-available-roles' );
			$pur_roles = is_array( $pur_roles ) ? $pur_roles : array();
		} else if ( $pur_control == 'block' ) {
			$pur_roles = $this->get_item_meta( $item_type, $item_id, 'pur-blocked-roles' );
			$pur_roles = is_array( $pur_roles ) ? $pur_roles : array();				
		} else {
			$pur_roles = array();
		}
		
		$wp_roles = new WP_Roles();
		$roles = $wp_roles->get_names();
		
		if ( is_array( $roles ) && count( $roles ) > 0 ) {
?>
<div style="padding:10px;">
<p>
<input type="radio" <?php echo 'no_control'==$pur_control?'checked="checked"':'';?> name="pur_control" default="default" value="no_control" />&nbsp;No control<br />
<input type="radio" <?php echo in_array($pur_control,array('','allow'))?'checked="checked"':'';?> name="pur_control" value="allow" />&nbsp;Allow access to checked roles<br />
<input type="radio" <?php echo 'block'==$pur_control?'checked="checked"':'';?> name="pur_control" value="block" />&nbsp;Block access to checked roles<br />
</p>
<hr />
<ul class="pur-roles">
<?php
			foreach($roles as $value => $label){
				$checked = in_array($value,$pur_roles)?'checked="checked"':'';
?>
<li><span><input type="checkbox" <?php echo $checked ?> name="pur_roles[]" value="<?php echo $value ?>" />&nbsp;<?php echo $label ?></span></li>
<?php
			}
			echo "</ul>";
?>
<br />
<label>No Access URL:</label><br />
<input type="text" style="width:98%;" name="pur_redir_url" value="<?php echo $this->get_item_meta( $item_type, $item_id, 'pur_redir_url', true ); ?>" />

<?php if ( ! isset( $metabox['args']['show_in_nav'] ) || $metabox['args']['show_in_nav'] == 1 ) : ?>
<br />
<br />
<label><input type="checkbox" <?php echo '1'==get_post_meta($post->ID,'pur_show_in_nav',true)?'checked="checked"':''; ?> name="pur_show_in_nav" value="1" />&nbsp;<?php _e('Show in restricted users menu','pur') ?></label>	
<?php endif; ?>

</div>		
<?php			
		}else{
			echo __('Settings error, we could not identify any User Roles in the system.','pur');
		}
	}		
	
	function get_item_meta( $item_type, $item_id, $meta_name, $single = false ) {
		switch ( $item_type ) {
			case 'post':
				return get_post_meta( $item_id, $meta_name, $single );
				break;

			case 'user':
				return get_user_meta( $item_id, $meta_name, $single );
				break;
		}
	}

	public static function available_roles( $object_id, $action, $type, $value = '', $include_terms = false ) {
		return self::manage_roles( $object_id, $action, $type, 'available', $value, $include_terms );
	}

	public static function blocked_roles( $object_id, $action, $type, $value = '', $include_terms = false ) {
		return self::manage_roles( $object_id, $action, $type, 'blocked', $value, $include_terms );
	}

	protected static function manage_roles( $object_id, $action, $type, $meta_type, $value = '', $include_terms = false ) {
		$function_name = sprintf( '%s_%s_meta', $action, $type );

		if ( ! function_exists( $function_name ) || ! in_array( $meta_type, array( 'available', 'blocked' ) ) ) {
			return;
		}

		$args = array(
			$object_id,
			sprintf( 'pur-%s-roles', $meta_type )
		);

		if ( in_array( $action, array( 'add', 'update' ) ) ) {
			$args[] = $value;
		}
		
		$roles = call_user_func_array( $function_name, $args );

		if ( $include_terms ) {
			$method_name = $meta_type . '_roles';
		
			if ( ! method_exists( __CLASS__, $method_name ) ) {
				return;
			}

			foreach ( PUR_Options::get_taxonomies() as $taxonomy ) {
				$terms = wp_get_post_terms( $object_id, $taxonomy, array( 'fields' => 'ids' ) );

				if ( $terms ) {
					foreach ( $terms as $term_id ) {
						
						$term_roles = self::$method_name( $term_id, 'get', 'term' );
						
						if ( $term_roles ) {
							$roles = array_merge( $roles, $term_roles );
						}
					}
				}
			}
		}

		return $roles ? array_unique( $roles ) : array();
	}

	public static function redirect_url( $object_id, $action, $type, $value = '' ) {
		$function_name = sprintf( '%s_%s_meta', $action, $type );

		if ( ! function_exists( $function_name ) ) {
			return;
		}

		$args = array(
			$object_id,
			sprintf( 'pur-%s-roles', $meta_type )
		);

		if ( in_array( $action, array( 'add', 'update' ) ) ) {
			$args[] = $value;
		}
		
		return call_user_func_array( $function_name, $args );
	}
}

return new WP_PUR();