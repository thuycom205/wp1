<?php
if (!class_exists('Magenest_Learn_Question')) include_once LEARN_PATH.'question.php';
while ( have_posts() ) : the_post();

    global $post;
    $postId= $post_id = $post->ID;
    if (!class_exists('Magenest_Learn_Question')) include_once LEARN_PATH.'question.php';
    $answers = Magenest_Learn_Question::getAnswerByPostId($postId);

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
    <script type="text/javascript"  src="<?php echo LEARN_URL. '/assets/js/owl.carousel.js' ?>"></script>

    <script src="https://code.jquery.com/ui/jquery-ui-git.js" ></script>
    <script src="<?php echo LEARN_URL. '/assets/math.min.js' ?>" ></script>


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
                <a href=""><i class="fa fa-volume-up" aria-hidden="true"></i><span>Listen</span></a>
            </li>
        </ul>
    </div>
</div>

<span id="dummy"></span>

<div id="main" class="page-main">
    <div class="section-vol">
        <div class="list-vol owl-vol vol-items">
        <?php foreach ($answers as $ans) { ?>

            <div class="vol">
                <div class="vol-img">
                    <img src="<?php echo LEARN_URL?>/images/<?php echo $ans['en'] ?>.jpg">
                </div>
                <div class="vol-text">
                    <span class="world"><?php echo $ans['vn'] ?></span>
                    <span class="vietnamese"><?php echo $ans['en'] ?></span>
                    <div>
                        <a href="#"  target="#" onclick="playSound('<?php echo LEARN_URL?>/audio/<?php echo $ans['en'] ?>__us_1.mp3');" class="vol-volume"><i class="fa fa-volume-down" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>

        <?php  }  ?>
        </div>
        <div class="owl-buttons">
            <div class="owl-prev"><i class="fa fa-caret-left" aria-hidden="true"></i></div>
            <div class="perpage">
                <span>12 of 24</span>
                <div class="progressBar">
                    <div class="fill" style="width: 50%;"></div>
                </div>
            </div>
            <div class="owl-next"><i class="fa fa-caret-right" aria-hidden="true"></i></div>
        </div>
        </div>
    </div>

</body>
<script type="text/javascript">

    function playSound(soundfile) {
      //  evt.preventDefault();

        /*  document.getElementById("dummy").innerHTML=
         "<embed src=\""+soundfile+"\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";*/

        var audio = new Audio(soundfile);
        audio.play();
        return false;
    }

    $(document).ready(function () {
        var owl = $('.owl-vol');
        owl.owlCarousel({
            items:1,
            navRewind: true

        });


        $('.owl-next').click(function() {
            owl.trigger('next.owl.carousel');
        })

        $('.owl-prev').click(function() {

            owl.trigger('prev.owl.carousel');
        })

    });

</script>
</html>
