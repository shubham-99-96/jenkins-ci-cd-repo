(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    //$(window).scroll(function () {
//        if ($(this).scrollTop() > 300) {
//            $('.sticky-top').css('top', '0px');
//        } else {
//            $('.sticky-top').css('top', '-100px');
//        }
//    });
    $(window).scroll(function(){
		if ($(window).scrollTop() >= 300) {
			//$('nav').addClass('sticky-top');
			$('.sticky-top').css({'top':'0px', 'background-color': 'rgb(242, 246, 253)', 'position': 'fixed'});
			//$('nav div').addClass('visible-title');
		}
		else {
			//$('nav').removeClass('sticky-top');
			$('.sticky-top').css({'top':'-100px', 'background-color': 'transparent','width': '100%', 'position':'static'});
		}
	});
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });

//
//    // Header carousel
//    $(".header-carousel").owlCarousel({
//        autoplay: true,
//        smartSpeed: 1500,
//        items: 1,
//        dots: true,
//        loop: true,
//        nav : true,
//        navText : [
//            '<i class="bi bi-chevron-left"></i>',
//            '<i class="bi bi-chevron-right"></i>'
//        ]
//    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        autoplayHoverPause:true,
        smartSpeed: 3000,
        center: true,
        margin: 24,
        dots: true,
        loop: true,
        nav : false,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:3
            },
            992:{
                items:5
            }
        }
    });
    
    // Testimonials carousel
    $(".jobopening-carousel").owlCarousel({
      autoplay: stop,
      smartSpeed: 1000,
      center: true,
      margin: 24,
      dots: true,
      loop: true,
      nav : false,
      responsive: {
          0:{
              items:1
          },
          768:{
              items:3
          },
          992:{
              items:4
          }
      }
    });

})(jQuery);

//search box



// Text animation

  class TypeWriter {
    constructor(txtElement, words, wait = 3000) {
      this.txtElement = txtElement;
      this.words = words;
      this.txt = "";
      this.wordIndex = 0;
      this.wait = parseInt(wait, 8);
      this.type();
      this.isDeleting = false;
    }
  
    type() {
      // Current index of word
      const current = this.wordIndex % this.words.length;
      // Get full text of current word
      const fullTxt = this.words[current];
  
      // Check if deleting
      if (this.isDeleting) {
        // Remove char
        this.txt = fullTxt.substring(0, this.txt.length - 1);
      } else {
        // Add char
        this.txt = fullTxt.substring(0, this.txt.length + 1);
      }
  
      // Insert txt into element
      this.txtElement.innerHTML = `<span class="txt">${this.txt}</span>`;
  
      // change color for data-text
      this.txtElement.innerHTML = `<span class="txt" style="color: #292C73;">${this.txt}&nbsp;</span>`;
  
      // Initial Type Speed
      let typeSpeed = 200;
  
      if (this.isDeleting) {
        typeSpeed /= 2;
      }
  
      // If word is complete
      if (!this.isDeleting && this.txt === fullTxt) {
        // Make pause at end
        typeSpeed = this.wait;
        // Set delete to true
        this.isDeleting = true;
      } else if (this.isDeleting && this.txt === "") {
        this.isDeleting = false;
        // Move to next word
        this.wordIndex++;
        // Pause before start typing
        typeSpeed = 200;
      }
  
      setTimeout(() => this.type(), typeSpeed);
    }
  }
  

$('.count').each(function () {
    $(this).prop('Counter',0).animate({
        Counter: $(this).text()
    }, {
        duration: 6300,
        easing: 'swing',
        step: function (now) {
            $(this).text(Math.ceil(now));
        }
    });
});