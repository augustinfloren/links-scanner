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
            $links = response.data;
            console.log($links)
            $.each($links, function(index, link) {
              $( "#scan-result" ).append( 
                `
                <li class="scan-result-item">
                  <div>
                    <p>${link.anchor_text}<p>
                  </div>
                  <span class="dashicons dashicons-admin-links"></span>
                  <a class="link" href="${link.url}">
                    ${link.url}
                  </a>
                </li>
                ` 
              )
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