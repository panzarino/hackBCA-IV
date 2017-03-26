/**
 * jTinder initialization
 */
$("#tinderslide").jTinder({
	// dislike callback
    onDislike: function (item) {

    },
	// like callback
    onLike: function (item) {

    },
	animationRevertSpeed: 200,
	animationSpeed: 400,
	threshold: 1,
	likeSelector: '.like',
	dislikeSelector: '.dislike'
});

/**
 * Set button action to trigger jTinder like & dislike.
 */
$('.actions .like, .actions .dislike').click(function(e){
	e.preventDefault();
	$("#tinderslide").jTinder($(this).attr('class'));
});

$('#addSongForm').ajaxForm({
	url: '/api/addsong',
	success: function (response) {
		if (response == "Fail"){
            $('#addSongResponse').html('<div class="alert alert-danger" role="alert"> Could not add that song to the playlist. </div>');
		}
		else {
            $('#addSongResponse').html('<div class="alert alert-success" role="alert"> Successfully added '+response+' to the playlist. </div>');
		}
	}
});
