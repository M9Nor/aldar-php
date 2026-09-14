$('#myTab .nav-link').click(function(e) {
    e.preventDefault(); // Prevent the browser from handling the link normally, this stops the page from jumping around. Remove this line if you do want it to jump to the anchor as normal.
    var linkHref = $(this).attr('href'); // Grab the URL from the link
    var url = $('#myTab').data('url'); // Grab the URL from the link
    var base_url = $('meta[name="app-url"]').attr("content")
    window.location.href = url + linkHref;
});