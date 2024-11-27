jQuery(document).ready(function ($) {
  console.log(scanButton.ajax_url);
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
            alert(response.data);
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