<?php
/**
 * 
 * This php script handel all AJAX request in the plugin
 * 
 * 
 */

 class wpid_ajax{

    function __construct(){

      add_action( "wp_ajax_wpid_request_competencies_qa", array($this, "wpid_request_competencies_qa" ) );

    }

    function wpid_request_competencies_qa(){

      $nonce = ( isset( $_POST['wp_nonce'] ) && !empty( $_POST['wp_nonce'] ) ) ? $_POST['wp_nonce'] : null ;
      $tax = $_POST['core_selections'];
      $args = array(
         "post_type"=>"wpid_questions",
         "posts_per_page"=>-1,
         "tax_query"=>array(
            array(
            "taxonomy"=>"core-competencies",
            "field"=>"slug",
            "terms"=>$tax
            )
         )
      );

      $competencies_qa = new WP_Query( $args );

      $all_qa = "<div>";
      $current_tax = null;
      if( $competencies_qa->have_posts() ){
            while( $competencies_qa->have_posts() ){
               $competencies_qa->the_post();
               $ID = get_the_ID();
               $title = get_the_title( $ID );
               $current_cat = get_the_terms( $ID, "core-competencies" ) ;

               if( $current_tax != null && $current_tax != $current_cat[0]->slug ){
                  $all_qa .= "</ul>";
                  $all_qa .= "<ul class='wpid-main-container'><h3>" . $current_cat[0]->name . "</h3>";
                  $x=1;
               }elseif( $current_tax != $current_cat[0]->slug && $current_tax == null ){
                  $all_qa .= "<ul class='wpid-main-container'><h3>" . $current_cat[0]->name . "</h3>";
                  $x=1;
               }

               $current_tax = $current_cat[0]->slug;

               $all_qa .= "<li>";

               $all_qa .=  wpid_lib::display_checkbox_options("wpid_" . $current_tax ,$title, "wpid_" . $current_tax . "_" . $x ); 

               $all_qa .= "</li>";


               $x++;
            }
      }

      $all_qa .= "</div>";

      wp_reset_postdata();

      if( $nonce != null && wp_verify_nonce( $nonce, "wpid_request_competencies_qa" ) ){
         die( json_encode( array("code"=>200, "requestEd_data"=> $_POST['core_selections'], "response"=>$all_qa ) ) );
      }else{
         die( json_encode( array("code"=>400) ) );
      }

    }
    
 }
 new wpid_ajax();