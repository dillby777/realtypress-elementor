<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class RPS_Elementor_Dynamic_Tag extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'rps-listing-fields';
	}

	public function get_title() {
		return esc_html__( '[RealtyPress] Listing Fields', 'rps-elementor-dynamic-tag' );
	}

	public function get_group() {
		return [ 'rps-elementor' ];
	}

	public function get_categories() {
		return [ 
			\Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
		    	\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::COLOR_CATEGORY,	
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
		    ];
	}
	
	protected function register_controls() {
		$rps_groups = [];
	    $current_post_id = get_the_ID();
	    $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );
	    $property = $crud->get_post_listing_details($current_post_id);
	    $property = $crud->categorize_listing_details_array($property);
	    
	    foreach ( array_keys( $property ) as $group) {
			$rps_groups[ $group ] = ucwords( str_replace( '_', ' ', $group ) );
		}

	    $this->add_control('rps_listing_group',
    		[
    			'type' => \Elementor\Controls_Manager::SELECT,
    			'label' => __( 'RPS Field Group', 'realtypress-elementor-dynamic-tags'),
    			'options' => $rps_groups,
    		]
    	);
	    
	    foreach ( array_keys( $property ) as $group) {
	        $rps_fields = [];
	        foreach ( array_keys( $property[$group] ) as $fields) {
			    $rps_fields[ $fields ] = ucwords( str_replace( '_', ' ', $fields ) );
		    }

			$this->add_control('rps_listing_field__'.$group,
                [
                    'label' => __( 'RPS `'.$group.'` Fields', 'realtypress-elementor-dynamic-tags'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => $rps_fields,
                    'condition' => [
                        'rps_listing_group' => $group,
                    ],
                ]
            );
			
		}
	    
    	$this->add_control(	'RPS Callbacks',
			[
				'label' => __( 'RPS Callbacks', 'realtypress-elementor-dynamic-tags'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
				    'null' => 'Select if issues',
                    'RPS_Fix_Case' => 'RPS Fix Case',
                )
			]
		);
	}

	public function render() {
	    $settings = $this->get_settings_for_display();
        $selected_top_level_array = $settings['rps_listing_group'];
        $selected_group_fields = $settings['rps_listing_field__'.$selected_top_level_array];
    
	    $return ='';
	    $current_post_id = get_the_ID();
	    $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );
	    $property = $crud->get_post_listing_details($current_post_id);
	    $property  = $crud->categorize_listing_details_array($property);
	    $keys = array_keys($property);
	    
	    $user_selected_variable = $this->get_settings( 'Private Fields' );
	    $callback = $this->get_settings( 'RPS Callbacks' );

		if ( !$selected_top_level_array ||  !$selected_group_fields) {
			return;
		}

		if (is_array($property[$selected_top_level_array][ $selected_group_fields ])) {
            $return = "The variable is an array.";
        } else {
            $return = $property[$selected_top_level_array][ $selected_group_fields ];
        }
        
        if ($callback == 'RPS_Fix_Case'){
            echo rps_fix_case($return);
        }else{
            echo $return;
        }
	}
	
}

class RPS_Elementor_Dynamic_Tag__photos extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'rps-listing-field-photos';
	}

	public function get_title() {
		return esc_html__( '[RealtyPress] Photos Fields', 'elementor-rps-dynamic-tag' );
	}

	public function get_group() {
		return [ 'rps-elementor' ];
	}

	public function get_categories() {
		return [ 
			\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::MEDIA_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY,
			\Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY
		    ];
	}
	
	protected function register_controls() {
		$variables = ['single'=>'First Image','gallery'=>'Gallery'];
		$rps_photos=[];
		$current_post_id = get_the_ID();
	    $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );
	    $property = $crud->get_post_listing_details($current_post_id);
	    //$property  = $crud->categorize_listing_details_array($property);
	    $property_photos = $crud->get_local_listing_photos( $property['ListingID'] );
		
		$this->add_control('rps_photo_type',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'RPS Photo Type', 'realtypress-elementor-dynamic-tags'),
				'options' => $variables,
			]
		);
				
    		
        	$this->add_control(	'fallback',
			[
				'label' => __( 'Fallback', 'realtypress-elementor-dynamic-tags'),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			]
		);
	}

    public function get_value( array $options = array() ) {
	    $current_post_id = get_the_ID();
	    $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );
	    $property = $crud->get_post_listing_details($current_post_id);
	    $property['property-photos'] = $crud->get_local_listing_photos( $property['ListingID'] );
	    $property  = $crud->categorize_listing_details_array($property);
	    $keys = array_keys($property);
	    
	    $rps_photo_type = $this->get_settings( 'rps_photo_type' );      
        
        if ( $rps_photo_type == 'single') {
            $single_img = $property['property-photos'][0];
            $single_img = json_decode($single_img['Photos'], true);
            $img =[
                'url' => $single_img['LargePhoto']['filename']
                ];
            if (!$single_img['LargePhoto']['filename']){
                $img = $this->get_settings( 'fallback' );
            }
            
            return $img;
            
        } elseif ( $rps_photo_type == 'gallery') {
            foreach ($property['property-photos'] as $img) {
                $photos = json_decode($img['Photos'], true);
                $img_arr[] =[
                    'url' => $photos['LargePhoto']['filename']
                ];
            }

            return $img_arr;
        }else{
            return;
        }
	}

}
