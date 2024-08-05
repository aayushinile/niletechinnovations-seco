$( document ).ready(function() {
    $(".sidebar-dropdown > a").click(function() {
    $(".sidebar-submenu").slideUp(200);
      if (
      $(this)
        .parent()
        .hasClass("active")
    ) {
      $(".sidebar-dropdown").removeClass("active");
      $(this)
        .parent()
        .removeClass("active");
    } else {
      $(".sidebar-dropdown").removeClass("active");
      $(this)
        .next(".sidebar-submenu")
        .slideDown(200);
      $(this)
        .parent()
        .addClass("active");
    }
  });

  $(".toggle-sidebar").click(function() {
    $(".main-site").toggleClass("toggled");
  });
  $("#show-sidebar").click(function() {
   $(".main-site").addClass("toggled");
  });
     
});

$( document ).ready(function() {

$('#plants-slider').owlCarousel({
    loop:false,
    margin:10,
    nav:false,
    dots:false,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:3
        }
    }
})
});
$( document ).ready(function() {

Fancybox.bind("[data-fancybox]", {
  // Optionally disable "compact" mode
  // compact: false,

  Html: {
    autoSize: false,
  },
});
});