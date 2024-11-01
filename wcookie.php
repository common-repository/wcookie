<?php

/*
Plugin Name: wCookie
Description: The wCookie plugin for wordpress lets you display a cookie (or other legal) notice on your wordpress website and inform the visitors that your site uses cookies.
Author: Ajay Lulia
Version: 1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit;


register_activation_hook( __FILE__, 'wcookie_activate' );

function wcookie_activate() {
    $config = wcookie_defualt_configuration_array();
    update_option('wcookie_configuration_dP', 'bottom');
    update_option('wcookie_configuration_bT', 'I Accept');
    update_option('wcookie_configuration_bC', '#222');
    update_option('wcookie_configuration_tC', '#fff');
    update_option('wcookie_configuration_lC', '#6bceff');
    update_option('wcookie_configuration_bBC', '#de2424');
    update_option('wcookie_configuration_bTC', '#fff');
    update_option('wcookie_configuration_cC',  "This website uses cookies to ensure you get the best experience on our website <a href='#'>Privacy Policy</a>.");
}

add_filter('the_content', 'wcookie_wcookie_append_to_content');
function wcookie_wcookie_append_to_content( $content ) {
   $content= $content.wcookie_gethtml();
    return $content;
}

function wcookie_gethtml() {
    if(isset($_COOKIE['jsp-wCookie'])){
        return;
    }
    $config = json_decode(get_option( 'wcookie_configuration' ));
    if($config->displayPosition=="top")
        $add_class = "cookie_container_top";
    if($config->displayPosition=="bottom")
        $add_class = "cookie_container_bottom";
    if($config->displayPosition=="left")
        $add_class = "cookie_container_left";
    if($config->displayPosition=="right")
        $add_class = "cookie_container_right";
    if($config->displayPosition=="floating")
        $add_class = "cookie_container_floating";
    if($config->displayPosition=="rounded")
        $add_class = "cookie_container_rounded";
    

    echo "<style>
            .cookie_container {
                background: $config->backgroundColor;
                color: $config->textColor;
            }
            .cookie_container a{
                color: $config->linkColor;
            }

            .cookie_container .cookie_btn{
                background:$config->buttonBackgroundColor;
                color: $config->buttonTextColor;
            }
        </style>";
    
    echo "<div class='cookie_container $add_class' style=''>
            <a href='#' onclick='wcookie_set_wCookie();' class='cookie_btn'>$config->buttonText</a>           
            <p class='cookie_message'>$config->cookieContent</p>
         </div>";
}



add_action( 'admin_enqueue_scripts', 'wcookie_add_color_picker' );
function wcookie_add_color_picker( $hook ) {
    if( is_admin() ) {
        wp_enqueue_style( 'wp-color-picker' ); 
        wp_enqueue_script( 'custom-script-handle', plugins_url( 'custom-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
    }
}
add_action( 'wp_enqueue_scripts', 'wcookie_scripts_basic' );
function wcookie_scripts_basic()
{
    wp_enqueue_script( 'wcookie-script', plugins_url( '/js/wcookie.js', __FILE__ ), array( 'jquery' ));
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'wcookie-style', plugins_url( '/css/wcookie.css', __FILE__ ) );

}







// Plugin Backend Menu
add_action('admin_menu', 'wcookie_add_admin_menu');
function wcookie_add_admin_menu(){
    add_Menu_page('wCookie', 'wCookie', 'delete_posts', 'cookie-plugin', 'wcookie_cookieConfiguration');
}


add_action('admin_post_cookie_configuration', 'wcookie_cookieConfigurationSave');



function wcookie_cookieConfigurationSave() {
    if( isset( $_POST['wcookie_nonce'] ) && wp_verify_nonce( sanitize_text_field($_POST['wcookie_nonce']), 'wcookie_nonce') ) {
            update_option('wcookie_configuration_dP', sanitize_text_field($_POST['displayPosition']));
            update_option('wcookie_configuration_bT', sanitize_text_field($_POST['buttonText']));
            update_option('wcookie_configuration_bC', sanitize_text_field($_POST['backgroundColor']));
            update_option('wcookie_configuration_tC', sanitize_text_field($_POST['textColor']));
            update_option('wcookie_configuration_lC', sanitize_text_field($_POST['linkColor']));
            update_option('wcookie_configuration_bBC', sanitize_text_field($_POST['buttonBackgroundColor']));
            update_option('wcookie_configuration_bTC', sanitize_text_field($_POST['buttonTextColor']));
            update_option('wcookie_configuration_cC', sanitize_text_field($_POST['cookieContent']));
            wp_redirect(get_admin_url(get_current_blog_id(), 'admin.php?page=cookie-plugin'));
    }
}


function wcookie_cookieConfiguration() { ?>
    <div class="wrap">
    <h2 class="plugin_heading">wCookie</h2>
    <div id="col-left">    
     <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" id="">
                <?php settings_fields('wporg_options');
                    do_settings_sections('wporg');                  
                    $wcookie_nonce = wp_create_nonce('wcookie_nonce');
                    $config = new stdClass;
                    $config->displayPosition = get_option('wcookie_configuration_dP');
                    $config->buttonText = get_option('wcookie_configuration_bT');
                    $config->backgroundColor = get_option('wcookie_configuration_bC');
                    $config->textColor = get_option('wcookie_configuration_tC');
                    $config->linkColor = get_option('wcookie_configuration_lC');
                    $config->buttonBackgroundColor = get_option('wcookie_configuration_bBC');
                    $config->buttonTextColor = get_option('wcookie_configuration_bTC');
                    $config->cookieContent = get_option('wcookie_configuration_cC');
                ?>
                <?php 
                    $themes = wp_get_themes();
                    $current = wp_get_theme()->get('Name');
                    foreach( $themes as $theme ){
                        if ($theme->get('Name')==$current){
                            $background  = esc_url($theme->get_screenshot());
                        };
                    }
                ?>

                <input type="hidden" name="action" value="cookie_configuration">
                <input type="hidden" name="wcookie_nonce" value="<?php echo $wcookie_nonce ?>">
                
                <table class="form-table">
                    <tr>
                    <td><label>Display Postion</label>
                    <td><select name="displayPosition">
                    <option value="top" <?=$config->displayPosition=='top'?'selected':''; ?> >Top</option>
                    <option value="bottom" <?=$config->displayPosition=='bottom'?'selected':''; ?>>Bottom</option>
                    <option value="left" <?=$config->displayPosition=='left'?'selected':''; ?>>Bottom-Left</option>
                    <option value="right" <?=$config->displayPosition=='right'?'selected':''; ?>>Bottom-Right</option>
                    <option value="floating" <?=$config->displayPosition=='floating'?'selected':''; ?>>Floating</option>
                    <option value="rounded" <?=$config->displayPosition=='rounded'?'selected':''; ?>>Floating Rounded</option>
                    </select></td>
                    </tr>

                    <tr>
                    <td><label>Button Text</label>
                    <td><input type="text" name="buttonText" value="<?=$config->buttonText; ?>">
                    </tr>

                    <tr>
                    <td><label>Background Color</label>
                    <td><input type="text" name="backgroundColor" class="color-field" value="<?=$config->backgroundColor; ?>">
                    </tr>

                    <tr>
                    <td><label>Text Color</label>
                    <td><input type="text" name="textColor" class="color-field" value="<?=$config->textColor; ?>">
                    </tr>

                    <tr>
                    <td><label>Link Color</label>
                    <td><input type="text" name="linkColor" class="color-field" value="<?=$config->linkColor; ?>">
                    </tr>

                    <tr>
                    <td><label>Button Background Color</label>
                    <td><input type="text" name="buttonBackgroundColor" class="color-field" value="<?=$config->buttonBackgroundColor; ?>">
                    </tr>

                    <tr>
                    <td><label>Button Text Color</label>
                    <td><input type="text" name="buttonTextColor" class="color-field" value="<?=$config->buttonTextColor; ?>">
                    </tr>

                    <tr>
                    <td><label>Cookie Content</label>                    
                    </tr>
                    <tr><td colspan="2"><textarea name="cookieContent" rows="5" cols="50"><?=stripslashes($config->cookieContent); ?></textarea></tr>
                </table>
                <button class="button">Save Configuration</button>   
                                            
</form>
</div>
<div id="col-right">
    <div id="preview">
    <span class="dashicons dashicons-external" style="right: 6px;cursor: pointer;top: 40px;position: absolute;" onclick="wcookie_doFullScreen()"></span>
        <div id="msgDiv">
                <button id="button"></button>            
                <p id="message">Demo Text</p>
                
        </div>
    </div>
</div>
</div>
<style type="text/css">
*:fullscreen
*:-ms-fullscreen,
*:-webkit-full-screen,
*:-moz-full-screen {overflow: auto !important;}
    #preview{margin: 0 20px;width: 50em;border: 2px solid #ccc;height: 30em; position: relative;background-size: cover;
    background-position: center;background-image: url(<?php echo $background; ?>)}
    #msgDiv{}
    .class_top{height:40px;position: absolute;top:0;width: 100%;line-height:2.5;}
    .class_bottom{height:40px;position: absolute;bottom:0;width: 100%;line-height:2.5;}
    .class_floating{height:40px;position: absolute;bottom:0;margin: 2% 2%; width: 96%;line-height:2.5;}
    .class_round {height:40px;position: absolute;bottom:0;margin: 2% 2%; width: 96%;line-height:2.5; border-radius: 3em;}
    .class_left{height:5em;position: absolute;bottom:0; width: 40%;bottom: 3%;left: 2%;padding: 10px;}
    .class_right{height:5em;position: absolute;bottom:0;width:40%;bottom: 3%;right: 2%;padding: 10px;}
    #message{display: inline;font-size: 9px;}
    .class_top #message, .class_bottom #message, .class_floating #message, .class_round #message  {margin: 0 0 0 6%;}
    #button{height:26px; font-size:11px; margin: 5px 10%;border: none;border-radius: 5px;float: right;}
    .wrap>h2.plugin_heading {margin: 10px 0; font-size: 20px; font-weight: 500; margin-bottom: 20px; border-bottom: 1px solid #ddd;}
    .form-table td{padding:7px 10px;}
</style>
<script type="text/javascript">

        function wcookie_doFullScreen(){
            document.getElementById('preview').webkitRequestFullScreen();
        }
    (function( $ ) {


    $(function() {
        $('.color-field').wpColorPicker({
            change: function (event, ui) {
            var element = event.target;
            var color = ui.color.toString();
            $(this).val(color);            
            wcookie_changePreview();
        },
        });
    });

    wcookie_renderDiv();
    function wcookie_renderDiv() {
        console.log($("textarea").val());
        $("#message").html($("textarea").val());
        $("#button").html($("input[name=buttonText]").val());
        $("#msgDiv").css({'background':$("input[name=backgroundColor]").val() });
        $("#msgDiv #message").css({'color':$("input[name=textColor]").val() });
        $("#msgDiv #message a").css({'color':$("input[name=linkColor]").val() });
        $("#button").css({'background':$("input[name=buttonBackgroundColor]").val(), 'color':$("input[name=buttonTextColor]").val() });
        var position =$("select[name=displayPosition]").val();
        if(position=='top'){

            $("#msgDiv").removeClass().addClass('class_top');
        }
        if(position=='bottom'){
            $("#msgDiv").removeClass().addClass('class_bottom');
        }
        if(position=='left'){
            $("#msgDiv").removeClass().addClass('class_left');
        }
        if(position=='right'){
            $("#msgDiv").removeClass().addClass('class_right');
        }
        if(position=='floating'){
            $("#msgDiv").removeClass().addClass('class_floating');
        }
        if(position=='rounded') {
            $("#msgDiv").removeClass().addClass('class_round');   
        }

    }
    $("textarea, input[name=buttonText]").blur(function(){
        wcookie_renderDiv();
    })

    $("select[name=displayPosition]").change(function(){
        wcookie_renderDiv();
    })


    function wcookie_changePreview(){
        wcookie_renderDiv();
    }
    
     
})( jQuery );
</script>
<?php
}
?>