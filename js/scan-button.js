jQuery(document).ready(function ($) {
  $scanButton = $("#scan-button");
  $loadIcon = $(".lds-ring");
  $scanButton.click(function() {
    $loadIcon.css("visibility", "visible");
    $scanButton.prop('disabled', true);
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
          $(".result-count").text(`Résultats : ${$links.length}`);
          $tbody = $("#scan-result tbody");
          $tbody.empty();
          $("#scan-result-container").css("visibility", "visible");
          $.each($links, function(index, link) {
            $tbody.append( 
              `
                <tr class=".rows">
                  <td>${link.anchor_text}</td>
                  <td><a href="${link.url}">${link.url}</a></td>
                  <td>${link.title}</td>
                  <td>${link.post_id}</td>
                  <td class="url ${link.status == 200 ? "ok" : "broken"}">${link.status}</td>
                </tr>
              ` 
            )
          })
          $loadIcon.css("visibility", "hidden");
          $scanButton.prop('disabled', false);
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