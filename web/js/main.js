(function(){

    function movieClicked(e) {
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
    }

    function showPreloader() {
        $("#details_content").empty();
        $("#preloader").show();
    }

    function hidePreloader(){
        console.log("yo");
        $("#preloader").hide();
    }

    function removeMovieVisuals(){
        var $next = $("#main_zone .selected").next();
        $("#main_zone .selected").slideUp(200, function(){
            $(this).remove();
            $("#details_content").empty();
            $next.click();
        });
    }

    function magnetLinkClicked(e){
        e.preventDefault();
        var $self = $(this);
        $.ajax({
            url: $self.attr("href")
        })
            .done(function(response){
                removeMovieVisuals();
                window.location = response.magnet;
            });
    }

    function waitOrRemoveLinkClicked(e){
        e.preventDefault();
        $self = $(this);
        $.ajax(
            {
                url: $self.attr("href")
            }
        ).done(function(response){
                removeMovieVisuals();
            });
    }

    (function init() {
        $("#main_zone").on("click", ".movie", movieClicked);
        $("#details_zone").on("click", ".magnet_link", magnetLinkClicked);
        $("#details_zone").on("click", ".remove_link", waitOrRemoveLinkClicked);
        $("#details_zone").on("click", ".wait_link", waitOrRemoveLinkClicked);
        $(".movie").first().click();
    })();
})();