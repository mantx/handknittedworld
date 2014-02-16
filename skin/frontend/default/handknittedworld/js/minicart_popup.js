/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


// show and hide mini cart popup    
jQuery(function($) {

  var top_cart = $("#top-cart");
  var top_cart_link = $('#top-link-cart');

  // Hide Cart
  var timeout = null;
  function hideCart() {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(function() {
      timeout = null;
      top_cart.slideUp(300);
      top_cart_link.removeClass('over');
    }, 200);
  }

  // Show Cart
  function showCart() {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(function() {
      timeout = null;
      top_cart.slideDown(300);
      top_cart_link.addClass('over');
    }, 200);
  }
   
  // Link Cart         
  top_cart_link.bind('mouseover', showCart)
                    .bind('click', showCart)
                    .bind('mouseout', hideCart);

  // Cart Content
  top_cart.bind('mouseover', showCart)
               .bind('click', hideCart)
               .bind('mouseout', hideCart);     
});


