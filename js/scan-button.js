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
            $tbody = $("#scan-result tbody");
            $tbody.empty();
            $("#scan-result-container").css("visibility", "visible");
            $.each($links, function(index, link) {
              $tbody.append( 
                `
                  <tr class=".rows">
                    <td>${link.anchor_text}</td>
                    <td><a href="${link.url}">${link.url}</a></td>
                    <td>${link.post_id}</td>
                    <td>400</td>
                  </tr>
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