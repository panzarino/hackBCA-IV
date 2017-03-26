var count;

$(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "/api/list",
        success: function(data){
            count = data.length-1;
            for (var i=0; i<data.length; i++){
            	var num = i%5+1;
            	var info = data[i];
            	var uri = info.track.uri;
            	var img = info.track.album.images[1].url;
            	var name = info.track.name;
            	var artist = info.track.artists[0].name;
            	$('#cards').append(
            	    '<li class="pane'+num+'"> <div class="img" style="background: url(\''+img+'\') no-repeat scroll center center;"></div> <div>'+name+' by '+artist+'</div> <div class="like"></div> <div class="dislike"></div> </li>'
                );
			}
            /**
             * jTinder initialization
             */
            $("#tinderslide").jTinder({
                // dislike callback
                onDislike: dislike,
                // like callback
                onLike: like,
                animationRevertSpeed: 200,
                animationSpeed: 400,
                threshold: 1,
                likeSelector: '.like',
                dislikeSelector: '.dislike'
            });
        }
    });
});

var dislike = function(){
    console.log(count);
    count--;
};

var like = function(){
    $.ajax({
        type: "GET",
        url: "/api/upvote",
        data: {'track': count}
    });
    count--;
};

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
