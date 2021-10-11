$(document).ready(function () {
  $(".slider").slick({
    arrows: true,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    infinite: true,
    autoplaySpeed: 2000,
    prevArrow:
      '<a class="carousel-control-prev"  role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only"></span></a>',
    nextArrow:
      '<a class="carousel-control-next" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only"></span></a>',
  });

  var setDirectionState = function () {
    if (!$("#sort").val()) {
      $("#direction").attr("disabled", true);
    } else $("#direction").removeAttr("disabled");
  };
  setDirectionState();

  $("#filter").click(function (e) {
    e.preventDefault();
    var search = $("#search").val();
    var sort = $("#sort").val();
    var direction = $("#direction").val();

    const urljOb = new URL(window.location.href);
    var url = urljOb.origin + urljOb.pathname;
    if (search) url += "?search=" + search;
    if (sort) {
      url += (url.includes("?") ? "&" : "?") + "sort=" + sort;
      if (direction) url += "&direction=" + direction;
    }

    window.location.replace(url);
  });

  $("#sort").change(function () {
    setDirectionState();
  });
});
