<?php
require_once('./../../../../wp-config.php');
require_once('./../wp-hcard-commenting.php');

$path = hCardId::get_path();

header('Content-Type: application/javascript; charset=utf-8');
?>

jQuery(document).ready( function() {
  jQuery('#commentform').addClass('hcard-commenting');

  var html = ' <a id="hcard_enabled_link" href="http://microformats.org/wiki/hCard">(hCard Enabled)</a> '
           + ' <span id="ajax-loader" style="display: none;">Loading hCard</span> ';
  
  if (jQuery('#commentform a[@id=hcard_enabled_link]').length == 0) {
    jQuery('#commentform input[@id=url]').next().append(html);
  }
  
  jQuery('#hcard_enabled_link').click( function() {
    var url = jQuery("#commentform input[@id=url]").val();
    if (url == "") {
      jQuery("#commentform input[@id=url]").val("use this field for your hCard url");
    } else {
      jQuery.ajax({
        type: "GET",
        url: "<?php echo $path."/wp-hcard-commenting.php"; ?>",
        data: {url: url},
        dataType: "json",
        success: function(data) {
        	if (data.vcard.fn != "")
            jQuery("#commentform input[@id=author]").val(data.vcard.fn);
          if (data.vcard.email != "")
            jQuery("#commentform input[@id=email]").val(data.vcard.email);
          if (data.vcard.url != "")
            jQuery("#commentform input[@id=url]").val(data.vcard.url);
        }
      });
    }
      
    return false;
  });

  jQuery("#ajax-loader").ajaxStart(function(){
    jQuery(this).show();
  });

  jQuery("#ajax-loader").ajaxStop(function(){
    jQuery(this).hide();
  });

  jQuery('#commentform input[@id=url]').click( function() {
    var url = jQuery("#commentform input[@id=url]").val();
    if (url == "use this field for your hCard url") {
      jQuery("#commentform input[@id=url]").val("");
    }
  });
});