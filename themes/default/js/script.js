$(function () {
  $.fn.scrollTo = function (speed) {
      if (typeof(speed) === 'undefined')
          speed = 500;

      $('html, body').animate({
          scrollTop: ($(this).offset().top - 100)
      }, speed);
      
      return $(this);
  };

  $('.cover-container, .edit-cover-container').hover(function() { 
      $('.edit-cover-container').removeClass('hidden'); 
  });
  $('.cover-container, .edit-cover-container').mouseleave(function() {                  
     $('.edit-cover-container').addClass('hidden'); 
  });
  
  $('.avatar-container, .edit-avatar-container').hover(function() { 
      $('.edit-avatar-container').removeClass('hidden'); 
  });
  $('.avatar-container, .edit-avatar-container').mouseleave(function() {                  
     $('.edit-avatar-container').addClass('hidden'); 
  });
  
  $('[data-toggle="tooltip"]').tooltip(); 

  $('.player-video').hover(function() { 
      $('.icons').removeClass('hidden'); 
  });
  $('.player-video').mouseleave(function() {                  
     $('.icons').addClass('hidden'); 
  });

  $
  var hash = $('.main_session').val();
  $.ajaxSetup({ 
    data: {
        hash: hash
    },
    cache: false 
  });
  if ($(window).width() < 720) {
    $('ul li').click(function(e) {
       e.stopPropagation(); 
    }); 
    $('.video-info-element').removeClass('pull-right');
  } else {
    if (!$('.video-info-element').hasClass('pull-right')) {
      $('.video-info-element').addClass('pull-right');
    }
  }
});
 
 if ($(window).width() < 720) {
    $('ul li').click(function(e) {
     e.stopPropagation(); 
   }); 

 }

function scrollToTop() {
	  verticalOffset = typeof (verticalOffset) != 'undefined' ? verticalOffset : 0;
	  element = $('html');
	  offset = element.offset();
	  offsetTop = offset.top;
	  $('html, body').animate({
	    scrollTop: offsetTop
	  }, 300, 'linear');
}
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.thumbnail-preview img').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).attr("link")).select();
  document.execCommand("copy");
  $temp.remove();
  notif({
    msg: "Link copyed to clipboard",
    type: "default",
    fade:0,
    timeout:1500
  });
}
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function Wo_LikeSystem(id, type, this_, is_ajax, repeat) {
   if (!id || !type) {
      return false;
   }
   if (!$('#main-container').attr('data-logged') && $('#main-url').val()) {
   	  window.location.href = PT_Ajax_Requests_File() + 'login?to=' + $('#main-url').val();
        return false;
   }
   var result = 0;
   
   if (type == 'like') {
      var likes = $(this_).attr('data-likes');
      if ($(this_).attr('liked')) {
         result = Number(likes) - 1;
         $(this_).removeAttr('liked');
         $(this_).removeClass('active');
      } else {
         result = Number(likes) + 1;
         $(this_).attr('liked', true);
         $(this_).addClass('active');
      }
      $('#likes').text(numberWithCommas(result));
      $(this_).attr('data-likes', result);
      if ($('#dislikes-bar').attr('data-likes') > 0) {
         if ($('#dislikes-bar').hasClass('active')) {
             $('#dislikes-bar').removeAttr('disliked');
             $('#dislikes-bar').removeClass('active');
             result = Number($('#dislikes-bar').attr('data-likes')) - 1;
             $('#dislikes').text(numberWithCommas(result));
             $('#dislikes-bar').attr('data-likes', result);
         }
      }
   } else if (type == 'dislike') {
      var dislikes = $(this_).attr('data-likes');
      if ($(this_).attr('disliked')) {
         result = Number(dislikes) - 1;
         $(this_).removeAttr('disliked');
         $(this_).removeClass('active');
      } else {
         result = Number(dislikes) + 1;
         $(this_).attr('disliked', true);
         $(this_).addClass('active');
      }
      $(this_).attr('data-likes', result);
      $('#dislikes').text(numberWithCommas(result));
      if ($('#likes-bar').attr('data-likes') > 0) {
         if ($('#likes-bar').hasClass('active')) {
             $('#likes-bar').removeAttr('liked');
             $('#likes-bar').removeClass('active');
             result = Number($('#likes-bar').attr('data-likes')) - 1;
             $('#likes').text(numberWithCommas(result));
             $('#likes-bar').attr('data-likes', result);
         }
      }
   }
   if (is_ajax == 'is_ajax') {
      $.post(PT_Ajax_Requests_File() + 'aj/like-system/' + type, {id: id, type:type});
   }
}

function PT_AddLike(id, this_, type , is_ajax) {
   if (!id || !type) { return false; }

   if (!$('#main-container').attr('data-logged') && $('#main-url').val()) {
        window.location.href = PT_Ajax_Requests_File() + 'login?to=' + $('#main-url').val();
        return false;
   }
    var result = 0;
    var main_comment = $('#comment-' + id);
   if (type == 'like') {
      var likes = $(this_).attr('data-likes');
      if ($(this_).attr('liked')) {
         result = Number(likes) - 1;
         $(this_).removeAttr('liked');
         $(this_).removeClass('active');
      } else {
         result = Number(likes) + 1;
         $(this_).attr('liked', true);
         $(this_).addClass('active');
      }
      main_comment.find('#comment-likes').text(numberWithCommas(result));
      $(this_).attr('data-likes', result);
   }
   if (type == 'dislike') {
      var likes = $(this_).attr('data-likes');
      if ($(this_).attr('liked')) {
         result = Number(likes) - 1;
         $(this_).removeAttr('liked');
         $(this_).removeClass('active');
      } 

      else {
         result = Number(likes) + 1;
         $(this_).attr('liked', true);
         $(this_).addClass('active');
      }
      main_comment.find('#comment-likes').text(numberWithCommas(result));
      $(this_).attr('data-likes', result);
   } 
   if (is_ajax == 'is_ajax') {
      $.post(PT_Ajax_Requests_File() + 'aj/comment-like-system/' + type, {id: id, type:type});
   }
}

var PT_Delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();

function PT_progressIconLoader(container_elem) {
  container_elem.each(function() {
    progress_icon_elem = $(this).find('i.progress-icon');
    default_icon = progress_icon_elem.attr('data-icon');
    hide_back = false;
    if (progress_icon_elem.hasClass('hidde') == true) {
      hide_back = true;
    }
    if ($(this).find('i.fa-spinner').length == 1) {
      progress_icon_elem.removeClass('fa-spinner').removeClass('fa-spin').addClass('fa-' + default_icon);
      if (hide_back == true) {
        progress_icon_elem.hide();
      }
    } else {
      progress_icon_elem.removeClass('fa-' + default_icon).addClass('fa-spinner fa-spin').show();
    }
    return true;
  });
}

function PT_HasExtension(id, exts) {
    var fileName = $(id).val();
    return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
}




function pt_elexists(el){
  return ($(el).length > 0);
}


function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function makeid() {
  var text = "";
  var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < 10; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
}

function escapeHTML(string) {
    var pre = document.createElement('pre');
    var text = document.createTextNode( string );
    pre.appendChild(text);
    return pre.innerHTML;
}

var lastScrollTop = 0;
$('.user-messages').scroll(function(event){
   var st = $(this).scrollTop();
   if (st > lastScrollTop){
       $('#load-more-messages').css('display', 'none');
   } else {
       $('#load-more-messages').css('display', 'block');
   }
   lastScrollTop = st;
});

Object.defineProperty(HTMLMediaElement.prototype, 'playing', {
    get: function(){
        return !!(this.currentTime > 0 && !this.paused && !this.ended && this.readyState > 2);
    }
})