jQuery(document).ready(function ($) {
  $('#scan-button').click(function() {
      const nonce = $(this).data('nonce');
      
      $.ajax({
        type: "POST",
        url: scanButton.ajax_url,
        data: { 
          action: 'scan_button_action',
          nonce: nonce,
        },
        success: function (response) {
          if (response.success) { 
            $links = response.data.links;
            $.each($links, function(index, link) {
              console.log(link)
              $( "#scan-result" ).append( `<li>${link}</li>` )
            })
          } else {
            alert('Erreur : ' + response.data);
          }
        },
        error: function () {
          alert('Une erreur est survenue.');
        },
      });
  });
});