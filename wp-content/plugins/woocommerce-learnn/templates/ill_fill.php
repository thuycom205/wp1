<!DOCTYPE html>
<html>

<?php
if (!class_exists('Magenest_Learn_Question')) include_once LEARN_PATH.'question.php';

while ( have_posts() ) : the_post(); ?>
<!--
    <?php
$postId= $post->ID;
echo $postId;
$attachments = get_posts( array(
    'post_type'   => 'attachment',
    'numberposts' => -1,
    'post_status' => null,
    'post_parent' => $post->ID
) );

if ( $attachments ) {
    foreach ($attachments as $attachment) {
    // $image = wp_get_attachment_image( $attachment->ID, 'full' );
        $image = wp_get_attachment_image_src( $attachment->ID,'full', false);
        if ( $image ) {
            list($src, $width, $height) = $image;
            echo $src;
        }
    }
}

$question = get_post_meta($postId, '_learn_question' ,true);
$question_ana = explode('%',$question);

var_dump($question_ana);

    ?>
 -->
<?php endwhile; // end of the loop.
?>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
    <script src="http://localhost/woomodule/fue/wp-content/plugins/woocommerce-learn/assets/CanvasInput.js"></script>
    <script id="editable">
        (function() {

            function Button(label, color) {
                this.Container_constructor();

                this.color = color;
                this.label = label;

                this.setup();
            }
            var p = createjs.extend(Button, createjs.Container);


            p.setup = function() {
                var text = new createjs.Text(this.label, "20px Arial", "#000");
                text.textBaseline = "top";
                text.textAlign = "center";

                var width = text.getMeasuredWidth()+30;
                var height = text.getMeasuredHeight()+20;

                text.x = width/2;
                text.y = 10;

                var background = new createjs.Shape();
                background.graphics.beginFill(this.color).drawRoundRect(0,0,width,height,10);

                this.addChild(background, text);
                this.on("click", this.handleClick);
                this.on("rollover", this.handleRollOver);
                this.on("rollout", this.handleRollOver);
                this.cursor = "pointer";

                this.mouseChildren = false;

                this.offset = Math.random()*10;
                this.count = 0;
            } ;

            p.handleClick = function (event) {
                alert("You clicked on a button: "+this.label);
            } ;

            p.handleRollOver = function(event) {
                this.alpha = event.type == "rollover" ? 0.4 : 1;
            };

            window.Button = createjs.promote(Button, "Container");
        }());

        function init() {
            var stage = new createjs.Stage("demoCanvas");
            var input = new CanvasInput({
                canvas: document.getElementById('demoCanvas'),
                fontSize: 18,
                fontFamily: 'Arial',
                fontColor: '#212121',
                fontWeight: 'bold',
                width: 300,
                padding: 8,
                borderWidth: 1,
                borderColor: '#000',
                borderRadius: 3,
                boxShadow: '1px 1px 0px #fff',
                innerShadow: '0px 0px 5px rgba(0, 0, 0, 0.5)',
                placeHolder: 'Enter message here...',
                x:900,
                y:300
            });

            input.onsubmit(function() {
                var cautraloi = input._value;
                console.log(cautraloi);

                var value = input.value;
                console.log(value);
            });
            var circle = new createjs.Shape();
            circle.graphics.beginFill("DeepSkyBlue").drawCircle(0, 0, 50);
            circle.x = 100;
            circle.y = 100;
            // circle.text = "Lost";
            //  stage.addChild(circle);
            var image = new Image();
            image.src = "s1.png";
            var container = new createjs.Container();
            stage.addChild(container);

            var bitmap = new createjs.Bitmap('<?php echo $src ?>');

            bitmap.x =100;

            bitmap.y= 100;

            // bitmap.rotation = 360 * Math.random() | 0;
            bitmap.regX = bitmap.image.width / 2 | 0;
            bitmap.regY = bitmap.image.height / 2 | 0;
            bitmap.scaleX = bitmap.scaleY = bitmap.scale = Math.random() * 0.4 + 0.6;
            bitmap.name = "bmp_" + i;
            bitmap.cursor = "pointer";
            container.addChild(bitmap);


            var textt = new createjs.Text("<?php echo $question_ana[0] ?>", "bold 24px Arial", "#ff7700");
            textt.x = 200;
            textt.y = 600;
            textt.textBaseline = "alphabetic";
            stage.addChild(textt);
            console.log(textt.getMeasuredWidth());

            var xofText2 = 200 + textt.getMeasuredWidth();

            console.log(textt.maxWidth);

            var textt3 = new createjs.Text("<?php echo $question_ana[2] ?>", "bold 24px Arial", "#ff7700");
            textt3.x = xofText2 + 150;
            textt3.y = 600;
            textt3.textBaseline = "alphabetic";
            stage.addChild(textt3);




            stage.update();

            var background = new createjs.Shape();
            background.name = "background";
            background.graphics.beginFill("DeepSkyBlue").drawRoundRect(0, 0, 150, 60, 10);

            var label = new createjs.Text("Lost", "bold 24px Arial", "#FFFFFF");
            label.name = "label";
            label.textAlign = "center";
            label.textBaseline = "middle";
            label.x = 50;
            label.y = 60/2;

            <?php
            $answers = Magenest_Learn_Question::getAnswerByPostId($postId);

            if ($answers) {

                foreach ($answers as $key=>$answer) {
                    $enAns = $answer['en'];
                    ?>
            var button<?php echo $key?> = new Button("<?php echo $enAns?>", "#F00");

            button<?php echo $key?>.name = "button";
            button<?php echo $key?>.x = 70 + <?php echo $key *10?>;
            button<?php echo $key?>.y = 90;
            stage.addChild(button<?php echo $key?>);
            <?php
                }

            }
?>


            // set up listeners for all display objects, for both the non-capture (bubble / target) and capture phases:
            var targets = [button0,button1];
            for (var i=0; i<targets.length; i++) {
                var target = targets[i];
                // target.on("click", handleClick, null, false, null, false);
                //target.on("click", handleClick, null, false, null, true);
                //target.addEventListener("click", handleClick, false);
                //target.addEventListener("click", handleClick, true);

                target.on("pressmove",function(evt) {
                    // currentTarget will be the container that the event listener was added to:
                    evt.currentTarget.x = evt.stageX;
                    evt.currentTarget.y = evt.stageY;
                    // make sure to redraw the stage to show the change:
                    stage.update();
                });

                target.on("pressup", function(evt) {
                    alert("well done");
                    console.log(evt.target.dung);
                    console.log(evt.target);
                    console.log(evt.stageX);
                    console.log(evt.stageY);
                    console.log(evt);

                })
            }

            function handleClick(evt) {
                if (evt.currentTarget == stage && evt.eventPhase == 1) {
                    // this is the first dispatch in the event life cycle, clear the output.
                    // output.text = "target : eventPhase : currentTarget\n";
                }

                alert('click nut');
                console.log('click button');
                // output.text += evt.target.name+" : "+evt.eventPhase+" : "+evt.currentTarget.name+"\n";

                if (evt.currentTarget == stage && evt.eventPhase == 3) {
                    // this is the last dispatch in the event life cycle, render the stage.
                    stage.update();
                }
            }
            stage.update();
        }
    </script>


</head>
<body  onload="init();">
<style>
    #main {

    }
</style>
<div id="canvas">
    <div class="container">
        <a class="entry-icon">
            <span class="video-section"> </span>
            <span class="label">Video</span>
        </a>

        <a class="entry-icon">
            <span class="video-section"> </span>
            <span class="label">Learn</span>
        </a>

        <a class="entry-icon">
            <span class="video-section"> </span>
            <span class="label">Sentence</span>
        </a>
        <a class="entry-icon">
            <span class="video-section"> </span>
            <span class="label">Spell</span>
        </a>

        <a class="entry-icon">
            <span class="video-section"> </span>
            <span class="label">Listen</span>
        </a>
    </div>
    <canvas id="demoCanvas" width="1800" height="1500">
        <div id="main" >

        </div>
        <div id="sub">

        </div>
    </canvas>

</div>
</body>
</html>