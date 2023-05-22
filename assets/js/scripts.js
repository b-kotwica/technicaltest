/* jQuery */
jQuery(document).ready(function($) {
    $.ajax({
        url: wp_ajax_get_books.ajax_url,
        type: 'POST',
        data: {
            action: 'get_books',
            nonce: wp_ajax_get_books.nonce
        },
        success: function(response) {
            console.log(response);
        },
    });
});

/* JavaScript */
document.addEventListener('DOMContentLoaded', () => {
  fetch(wp_ajax_get_books.ajax_url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      action: 'get_books',
      nonce: wp_ajax_get_books.nonce
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
    })
    .catch((error) => {
      console.error(error);
    });
});