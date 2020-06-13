<!DOCTYPE html>
<html>

<?php
if (!class_exists('Magenest_Learn_Question')) include_once LEARN_PATH.'question.php';

while ( have_posts() ) : the_post(); ?>
    <!--
    <?php
    $postId= $post->ID;
    ?>
 -->
<?php endwhile; // end of the loop.
?>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
    <script src="http://localhost/woomodule/fue/wp-content/plugins/woocommerce-learn/assets/CanvasInput.js"></script>
    <script src="<?php echo LEARN_URL ?>/assets/Touch.js"></script>
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
               // this.on("click", this.handleClick);
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


            createjs.Touch.enable(stage);


            var background = new createjs.Shape();
            background.name = "background";
            background.graphics.beginFill("yellow").drawRoundRect(0, 0, 150, 60, 10);

            var label = new createjs.Text("Lost", "bold 24px Arial", "#FFFFFF");
            label.name = "label";
            label.textAlign = "center";
            label.textBaseline = "middle";
            label.x = 50;
            label.y = 60/2;
            var targets=[];
            <?php
            $answers = Magenest_Learn_Question::getAnswerByPostId($postId);
            $shuffleYs= array();
            if ($answers) {
                foreach ($answers as $key=>$answer) {
                   $shuffleYs[] = 90 + $key*50;
                }
            shuffle($shuffleYs);
            }

            if ($answers) {

                foreach ($answers as $key=>$answer) {
                    $enAns = $answer['en'];

                    $vnWord = $answer['vn'];
                    ?>
            var buttonvn<?php echo $key?> = new Button("<?php echo $vnWord?>", "#f0ff4c");

            buttonvn<?php echo $key?>.name = "button";
            buttonvn<?php echo $key?>.x = 70;
            buttonvn<?php echo $key?>.y = <?php echo $shuffleYs[$key]; ?>;
            stage.addChild(buttonvn<?php echo $key?>);

            targets.push(buttonvn<?php echo $key?>);

            var button<?php echo $key?> = new Button("<?php echo $enAns?>", "#F00");

            button<?php echo $key?>.name = "button";

            button<?php echo $key?>.dung = buttonvn<?php echo $key?>;
            button<?php echo $key?>.x = 270;
            button<?php echo $key?>.y = 90  + <?php echo $key *50?>;
            stage.addChild(button<?php echo $key?>);

            targets.push(button<?php echo $key?>);






            <?php
                }

            }
?>


            // set up listeners for all display objects, for both the non-capture (bubble / target) and capture phases:
           // var targets = [button0,button1];
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
                    // alert("well done");

                    match = evt.target.dung;
                    if (match != undefined) {

                    if (match.x - 25 < evt.stageX && evt.stageX < match.x + 25
                        && match.y - 25 < evt.stageY && evt.stageY < match.y +25
                    ) {

                        alert('ok');
                        console.log('target x ' + evt.stageX);
                        console.log('match x  ' + match.x);
                        // stage.remo\
                        match.removeAllChildren();
                        evt.target.removeAllChildren();
                        stage.update();

                    } else {
                        console.log('target x ' + evt.stageX);
                        console.log('match x  ' + match.x);
                    }

                }

                   // console.log(evt.target);
                   // console.log(evt.stageX);
                   // console.log(evt.stageY);
                   // console.log(evt);

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
    <canvas id="demoCanvas" width="1800" height="9500">
        <div id="main" >

        </div>
        <div id="sub">

        </div>
    </canvas>

</div>
</body>
</html>