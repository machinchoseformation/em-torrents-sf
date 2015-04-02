(function(){
    function init() {
        $("#main_zone").on("click", ".movie", function (e) {
            e.preventDefault();
            showPreloader();
            $(".movie").removeClass("selected");
            $(this).addClass("selected");
            var movieUrl = $(this).attr("href");
            $.ajax({
                url: movieUrl
            }).done(function (response) {
                $("#details_content").html($(response).find("#details_content").html());
            }).always(function(response){
                hidePreloader();
            });
        });
    }

    function showPreloader() {
        $("#details_content").empty();
        $("#preloader").show();
    }

    function hidePreloader(){
        console.log("yo");
        $("#preloader").hide();
    }

    init();
})();