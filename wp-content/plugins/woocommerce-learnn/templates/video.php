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
    <script type="text/javascript"  src="<?php echo LEARN_URL. '/assets/js/owl.carousel.js' ?>"></script>
    <script src="https://code.jquery.com/jquery-2.2.3.js" integrity="sha256-laXWtGydpwqJ8JA+X9x2miwmaiKhn8tVmOVEigRNtP4=" crossorigin="anonymous"></script>
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
                        <a href="<?php echo get_permalink( $post->ID ); ?>&type=listen"><i class="fa fa-volume-up" aria-hidden="true"></i><span>Listen</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <span id="dummy"></span>
        <?php
        echo get_post_meta($postId, '_learn_video' ,true);
        ?>

    </body>

</html>
