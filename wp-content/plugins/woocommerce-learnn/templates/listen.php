<?php
if (!class_exists('Magenest_Learn_Question')) include_once LEARN_PATH.'question.php';
while ( have_posts() ) : the_post();

    global $post;
    $postId= $post_id = $post->ID;

endwhile
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo LEARN_URL ?>/assets/css/owl.carousel.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,vietnamese' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="<?php echo LEARN_URL ?>/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-2.2.3.js" integrity="sha256-laXWtGydpwqJ8JA+X9x2miwmaiKhn8tVmOVEigRNtP4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/jquery-ui-git.js" ></script>
    <script src="<?php echo LEARN_URL. '/assets/math.min.js' ?>" ></script>
    <script>
        $(function() {
            $( "#drag li" ).draggable({
                appendTo: "body",
                helper: "clone"
            });
            $( ".placeholder" ).droppable({
                activeClass: "ui-state-default",
                hoverClass: "ui-state-hover",
                accept: ":not(.ui-sortable-helper)",
                drop: function( event, ui ) {
                    //$( this ).find( ".placeholder" ).remove();

                    console.log(ui.draggable.text());

                    var text = ui.draggable.text();
                    $(this).removeClass('correct').removeClass('wrong');
                    console.log($(this).data('digit'));
                    if (text.toLowerCase()  == $(this).data('digit').toLowerCase() ) {
                        $(this).data('correct', 'correct');
                        $(this).addClass('correct');

                    } else {
                        $(this).addClass('wrong');
                    }
                    $(this).html(text);
                    //  $(this).innerHTML( ui.draggable.text());

                    // $( "<span class='word'></span>" ).text( ui.draggable.text() ).appendTo( this );
                }
            }).sortable({
                items: "li:not(.placeholder)",
                sort: function() {
                    // gets added unintentionally by droppable interacting with sortable
                    // using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
                    $( this ).removeClass( "ui-state-default" );
                }
            });
        });
    </script>

</head>
<body>
<div class="header" id="header">
    <div class="nav-menu">
        <ul>
            <li>
                <a href="<?php echo get_permalink( $post->ID ); ?>&type=video"><i class="fa fa-video-camera" aria-hidden="true"></i><span>Video</span></a>
            </li>
            <li>
                <a href=""><i class="fa fa-book" aria-hidden="true"></i><span>Learn</span></a>
            </li>
            <li>
                <a href="<?php echo get_permalink( $post->ID ); ?>&type=flashcard"><i class="fa fa-flash" aria-hidden="true"></i><span>FlashCard</span></a>
            </li>
            <li>
                <a href=""><i class="fa fa-file-text-o" aria-hidden="true"></i><span>Sentence</span></a>
            </li>
            <li>
                <a href="<?php echo get_permalink( $post->ID ); ?>&type=spell"><i class="fa fa-font" aria-hidden="true"></i><span>Spell</span></a>
            </li>
            <li>
                <a href="<?php echo get_permalink( $post->ID ); ?>&type=listen"><i class="fa fa-volume-up" aria-hidden="true"></i><span>Listen</span></a>
            </li>
        </ul>
    </div>
</div>

<span id="dummy"></span>
<?php
$ills = get_post_meta($postId, '_listen_id' ,true);
$ids = explode(',' , $ills);
if ($ids) {
    $i = 1;
    foreach ($ids as $id) {

        ?>

        <ul>
            <li > <a  class="<?php echo $i .'question'?>" href="<?php echo   $_SERVER["REQUEST_URI"] ?>&id=<?php echo $id?>"> <?php echo $i ?> </a> </li>
        </ul>
<?php
        $i++;

    }
}
if (isset($_REQUEST['id'])) {
    $postId = $_REQUEST['id'];
    $posts = get_posts($postId);
    $post = $posts[0];
}
else {
    $postId = $ids[0];
    $posts = get_posts($postId);
    $post = $posts[0];
}

?>



<body>
<style>
    #player {
        display: block;
    }
    #drop-c {

        font-weight: bold;

        font-size: 34px;
    }
    #drag ul {
        list-style-type: none;
    }

    #drag ul li {
        display: inline;
        font-size: 27px;
        padding: 10px;
    }
    .digit {
        padding-top:20px;
        padding-bottom:20px;
        padding-right:10px;
    }
    .word {
        padding:20px
    }
    .placeholder {

        border: 1px solid #a9a9a9;
    }
    .illustration {
        width: 300px;
    }

    .correct {
        color:green;
    }

    .wrong {
        color:red;
    }
</style>
<!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
<div id="player" style="display: none"></div>

<script>
    // 2. This code loads the IFrame Player API code asynchronously.
    var tag = document.createElement('script');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // 3. This function creates an <iframe> (and YouTube player)
    //    after the API code downloads.
    var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            height: '390',
            width: '640',

            events: {
               // 'onReady': onPlayerReady,
              //  'onStateChange': onPlayerStateChange
            }
        });

    }

    // 4. The API will call this function when the video player is ready.
    function onPlayerReady(event) {
        event.target.loadVideoById({'videoId': '<?php echo get_post_meta($postId, '_learn_video_id' ,true)?>',
            'startSeconds': math.eval(<?php echo get_post_meta($postId, '_learn_start' ,true)?>),
            'endSeconds':  math.eval(<?php echo get_post_meta($postId, '_learn_end' ,true)?>),
            'suggestedQuality': 'large'});
        event.target.playVideo();
        return false

    }

    // 5. The API calls this function when the player's state changes.
    //    The function indicates that when playing a video (state=1),
    //    the player should play for six seconds and then stop.
    var done = false;
    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING && !done) {
            setTimeout(stopVideo, 6000);
            done = true;
        }
    }
    function stopVideo() {
        // player.stopVideo();
        //https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=barack%20obama" .
        // "v=1.0&q=barack%20obama
    }

    function plv() {

    }
    function nextAction() {
        var xz = 100;
        $('.placeholder').each (function (index, element) {
            //  alert('in here');
            var input = $(element).val();
            var answer = $(element).data('digit');

            if (input == answer) {
                alert('correct');

            } else {
                alert('wrong answer')
            }

        }) ;
    }
</script>
</body>

<div id="action-button">

<button id = "pl" > Play</button>
<button id="next" onclick="nextAction()" >Next</button>
</div>

<div id="drag">
    <ul>
        <?php
        $answers = Magenest_Learn_Question::getAnswerByPostId($postId);

        if ($answers) {

            foreach ($answers as $key => $answer) {

                $enAns = $answer['en'];

                ?>
                <li><?php echo $answer['en'] ?></li>
            <?php
            }
        }
        ?>
    </ul>
</div>
<div id="drop">
    <img style="display: none" class="illustration" src="<?php echo LEARN_URL?>/assets/s1.png" />
    <br>
    <div>
        <span>Enter the appropriate words</span>
    </div>
    <div id="drop-c" >

        <?php
        $inputR = get_post_meta($postId, '_learn_question_advanced' ,true);

        $result = preg_replace('/span/' , 'input' , $inputR);
        $result2 = preg_replace('/\./' , '' , $result);
        echo $result2;
        ?>
    </div>

</div>

<script>
    $(function() {
        $('#pl').click(function() {
            player.loadVideoById({'videoId': '<?php echo get_post_meta($postId, '_learn_video_id' ,true)?>',
                'startSeconds': math.eval(<?php echo get_post_meta($postId, '_learn_start' ,true)?>),
                'endSeconds':  math.eval(<?php echo get_post_meta($postId, '_learn_end' ,true)?>),
                'suggestedQuality': 'large'});
            player.playVideo();
        }) ;

    });
</script>
</body>

</html>