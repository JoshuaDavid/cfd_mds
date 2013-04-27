var imagePairs = <?php echo $pairs; ?>;
var faceLocations = <?php echo $fl; ?>;
var currentPair = 0;
$(document).ready(function() {
    showFaces();
    $('input.similarityButton').click(function() {
        if(!$(this).attr('disabled')) {
            var request = {
                'f1': imagePairs[currentPair][0],
                'f2': imagePairs[currentPair][1],
                'f1url': faceLocations[imagePairs[currentPair][0]],
                'f2url': faceLocations[imagePairs[currentPair][1]],
                'similarity': $(this).val(),
                'confirmation': $('.code').text(),
                'completed': currentPair + 1
            }
            console.log("Sending request");
            $.post('saveData.php', request, function(response) {
                console.log(response);
                //Makes sure they should still be submitting responses
                if(response.match(/Success/g) || response.match(/\d+/g)) {
                    currentPair = parseInt(response.match(/[\d]+/g)[0]);
                    showFaces();
                }
                else {
                    $('.similaritybuttons').html('<p>Congratulations! You finished your <? echo $job_size; ?> allotted problems! </p>');
                    console.log('done');
                    $('.faces').hide();
                    $('.similarityButtons').hide();
                    $('.debriefing').show();
                }
            });
        }
    });
});

function showFaces() {
    if(currentPair < <? echo $job_size ?>) {
        $('.faces').children().remove();
        $('<img>')
            .addClass('f1')
            .attr('src', faceLocations[imagePairs[currentPair][0]])
            .appendTo($('.faces'));
        $('<img>')
            .addClass('f2')
            .attr('src', faceLocations[imagePairs[currentPair][1]])
            .appendTo($('.faces'));
        $('.f2').on('load', function() {
            $(this).parent().height($(this).height());    
        });
        $('.similaritybutton').attr('disabled', 'disabled');
        setTimeout(
            function() {
                $('.similaritybutton').removeAttr('disabled');
            }, 
            <? echo $cooldown ?>
        );
        //Progress bar: not strictly necessary, but nice to have.
        $('.progressinner').width(Math.floor(100 * currentPair / <? echo $job_size ?>)+'%');
    }
    else {
        window.location = "./demographics.php?confirmation=<? echo $confirmation; ?>";
    }
}
