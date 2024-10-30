(function ($) {
  $(document).ready(function () {
    var s = document.createElement("script");
    s.type = "module";
    s.setAttribute('crossorigin','anonymous')
    s.src = c4dShoppable.app_url + "/front.js";
    $("head").append(s);

    var s = document.createElement("link");
    s.rel = "modulepreload";
    s.setAttribute('crossorigin','anonymous')
    s.href = c4dShoppable.app_url + "/pinia.js";
    $("head").append(s);
  });
})(jQuery);
