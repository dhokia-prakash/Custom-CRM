<?php
// Exit if accessed directly
if (!defined('ABSPATH')) { exit; }
if (!class_exists('CUSTOM_POST')) {
    class CUSTOM_POST
    {
        /**
         * Holds a copy of itself, so it can be referenced by the class name.
         *
         * @since 3.5
         *
         * @var CUSTOM_POST
         */       
        public function __construct()
        {
            $this->init();            
                        
        }
        
        /**
         * Call when object of class created.
         */
        public function init()
        {
            /**
             * Activate plugin and create custom post type with Wordpress
             */               
                       
            add_action('init', array($this, 'custom_post_type'));	    	
	    	add_action( 'admin_init', array($this, 'add_post_meta_box' ));
			add_shortcode( 'custom-form', array($this,'custom_form' ));
			add_action( 'wp_enqueue_scripts', array($this,'custom_enqueue_scripts' ) );

			add_action('wp_ajax_submit_contact_form', array($this, 'submit_contact_form') );
        	add_action('wp_ajax_nopriv_submit_contact_form', array($this,'submit_contact_form') );
        }

        /**
		 * Include scripts.
		 */
		public function custom_enqueue_scripts() {

			wp_enqueue_script( 'jquery-min', POST_ROOT_PATH . 'assets/js/jquery-1.11.3.min.js', array( 'jquery' ), false, null );
			wp_register_script( 'my-script', POST_ROOT_PATH . 'assets/js/my-script.js', wp_rand(), true );			
			wp_localize_script( 'my-script', 'ajax_object',array( 'ajaxurl' => admin_url('admin-ajax.php')) );
			wp_enqueue_script( 'my-script' );
		}

        /**
         * Check plugin class exists or not.
         */
        public static function is_custom_post_plugin_activate()
        {
            if (!function_exists('is_plugin_active')) {
                include_once ABSPATH.'wp-admin/includes/plugin.php';
            }
            if (!is_plugin_active('custom-post/functions.php')) {
                return false;
            }
            return true;
        }

        /**
         * Create custom post type of Customer.
         */
        public function custom_post_type()
        {      
		
			$labels = array(
			'name'               => _x( 'Customer', 'cyp-crm' ),
			'singular_name'      => _x( 'Customer', 'cyp-crm' ),
			'add_new'            => _x( 'Add New', 'Customer' ),
			'add_new_item'       => __( 'Add New Customer', 'cyp-crm' ),
			'edit_item'          => __( 'Edit Customer', 'cyp-crm' ),
			'new_item'           => __( 'New Customer', 'cyp-crm' ),
			'all_items'          => __( 'All Customers', 'cyp-crm' ),
			'view_item'          => __( 'View Customer', 'cyp-crm' ),
			'search_items'       => __( 'Search Customers', 'cyp-crm' ),
			'not_found'          => __( 'No Customers found', 'cyp-crm' ),
			'not_found_in_trash' => __( 'No Customers found in the Trash', 'cyp-crm' ),
			'menu_name'          => 'Customers'
			);
		
			/**
        	* Pass the args.
        	*/

			$args = array(
			'labels'        => $labels,
			'description'   => 'Customer',
			'public'        => true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'has_archive'   => true,
			);
		
			/**
        	* Call the default register function.
        	*/

			register_post_type( 'customer', $args );
             
       }
	
		/**
		 * Add custom Meta Box to Posts post type customer.
		 */
		public function add_post_meta_box()
		{
		    
		    add_meta_box(
		        'phone_number',
		        'Phone',
		        array( $this,'cust_phone'),
		        'customer',
		        'normal',
		        'core'
		    );

		    add_meta_box(
		        'email_text',
		        'Email',
		        array( $this,'cust_email'),
		        'customer',
		        'normal',
		        'core'
		    );

		    add_meta_box(
		        'budget',
		        'Desired Budget',
		        array( $this,'cust_budget'),
		        'customer',
		        'normal',
		        'core'
		    );
		}

		/**
		 * Add custom meta box Phone for post type customer.
		 */	
		public function cust_phone() 
		{
		    global $post;		   
			$phone_number = get_post_meta( $post->ID, 'phone_number', true );
		?>
		<div id="dynamic_form">
		    <div id="field_wrap">
		    <?php
		    if ( isset( $phone_number ) ) 
		    {	        
		        ?>
		          	<input type="text" name="phone_number" rows="4" cols="50" value="<?php esc_html_e( $phone_number ); ?>">
		        <?php
		        
		    } else { ?>
		    	<div style="display:block;" id="master-row">	    
		            <div class="form_field">
		            	<input type="text" name="phone_number" rows="4" cols="50" value="<?php esc_html_e( $phone_number ); ?>">
		            </div>
		    </div>
		    <?php }// endforeach
		    ?>
		    </div>	    
		</div>
		  <?php
		}

		/**
		 * Add custom meta box Email for post type customer.
		 */
		public function cust_email(){
			global $post;

			$email_text = get_post_meta( $post->ID, 'email_text', true ); ?>
			<div id="dynamic_form">
			    <div id="field_wrap">
			    <?php
			    if ( isset( $email_text ) ) 
			    {	        
			        ?>
			          	<input type="text" name="email_text" rows="4" cols="50" value="<?php esc_html_e( $email_text ); ?>">
			        <?php
			        
			    } else { ?>
			    	<div style="display:block;" id="master-row">	    
			            <div class="form_field">
			            	<input type="text" name="email_text" rows="4" cols="50" value="<?php esc_html_e( $email_text ); ?>">
			            </div>
			    </div>
			    <?php }// endforeach
			    ?>
			    </div>
			</div>
		<?php 
		}

		/**
		 * Add custom meta box Budget for post type customer.
		 */
		public function cust_budget(){

			global $post;
			$budget = get_post_meta( $post->ID, 'budget', true ); ?>
			<div id="dynamic_form">
			    <div id="field_wrap">
			    <?php
			    if ( isset( $budget ) ) 
			    {	        
			        ?>
			          	<input type="text" name="budget" rows="4" cols="50" value="<?php esc_html_e( $budget ); ?>">
			        <?php
			        
			    } else { ?>
			    	<div style="display:block;" id="master-row">	    
			            <div class="form_field">
			            	<input type="text" name="budget" rows="4" cols="50" value="<?php esc_html_e( $budget ); ?>">
			            </div>
			    </div>
			    <?php }// endforeach
			    ?>
			    </div>
			</div>
		<?php 
		}

		/**
		 * Create shortcode wtih some parameters.
		 */	
		public function custom_form( $atts ) {
			ob_start();
			// define attributes and their defaults 
			extract( shortcode_atts( array (				
				'name' => 'Your Name',
				'max_length' => '20',
				'rows' => '5',
				'cols' => '20',
				
			), $atts ) );			
			?>
			<div class='custom-form'>
				<span> Customer Form</span>
				<form id="contact-form" method="post">
				    <div>
				        <label for="name"><?php echo $name; ?>:</label>
				        <input type="text" id="name" name="name" required maxlength="<?php esc_html_e ( $max_length ); ?>">
				    </div>
				    <div>
				        <label for="phone">Phone Number:</label>
				        <input type="tel" id="phone" name="phone" required maxlength="<?php esc_html_e ( $max_length ); ?>">
				    </div>
				    <div>
				        <label for="email">Email Address:</label>
				        <input type="email" id="email" name="email" required>
				    </div>
				    <div>
				        <label for="budget">Desired Budget:</label>
				        <input type="number" id="budget" name="budget" required>
				    </div>
				    <div>
				        <label for="message">Message:</label>
				        <textarea id="message" name="message" required rows="<?php esc_html_e ( $rows ); ?>" cols="<?php esc_html_e ( $cols ); ?>"></textarea>
				    </div>
				    <div>
				        <input type="submit" value="Submit">
				    </div>
				</form>
				<div id='msg'></div>
			</div>
			<?php
		}

		/**
		 * Save post action, process fields
		 */
		public function submit_contact_form()
		{
			if(isset($_POST['form_data'])){

				parse_str($_POST['form_data'], $searcharray);

				$my_cptpost_args = array(
				'post_title'    => $searcharray['name'],
				'post_content'  => $searcharray['message'],
				'post_status'   => 'private',
				'post_type' => 'customer'
				);

				// insert the post into the database				
				$post_id = post_exists( $searcharray['name'] );
				
				if($post_id){

					if ( $searcharray['phone'] && $searcharray['email'] && $searcharray['budget'] ) 
				    {
				        update_post_meta( $post_id, 'phone_number', $searcharray['phone'] );
				        update_post_meta( $post_id, 'email_text', $searcharray['email'] );
				        update_post_meta( $post_id, 'budget', $searcharray['budget'] );
				        $resp = array('message' => 'Customer is already exists, But other data is updated'); 
	    				wp_send_json($resp) ;			        
				       die;
				    } else {

				    	delete_post_meta( $post_id, 'phone_number' );
				    	delete_post_meta( $post_id, 'email_text' );
				    	delete_post_meta( $post_id, 'budget' );
				    	die;
				    }
				} else {

					$post_id = wp_insert_post( $my_cptpost_args );
					update_post_meta( $post_id, 'phone_number', $searcharray['phone'] );
				    update_post_meta( $post_id, 'email_text', $searcharray['email'] );
				    update_post_meta( $post_id, 'budget', $searcharray['budget'] );
					$resp = array('message' => 'Customer is added successfully!');
	    			wp_send_json($resp);
	    			die;
				}
			} else {
				
				wp_insert_post( $searcharray );
				$resp = array('message' => 'Customer is ont save, some issues!');
    			wp_send_json($resp);
    			die;
			}
			
		}

    }

}?>
