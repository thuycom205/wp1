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
        <script src="<?php echo LEARN_URL. '/assets/knockout-3.4.0.js' ?>" ></script>
        <style>
            .tbc {
                display: table-cell;
            }

            .tbr {
                display: table-row;
            }
            .right {
                border: 1px solid #0000ff;
                padding: 5px;
                background-color: #006699;
            }

            .wrong {
                border: 1px solid #ac0404;
                padding: 5px;
                background-color: #ff0000;
            }
        </style>

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
                <a href="<?php echo get_permalink( $post->ID ); ?>&type=qa"><i class="fa fa-font" aria-hidden="true"></i><span>QA</span></a>
            </li>
            <li>
                <a href="<?php echo get_permalink( $post->ID ); ?>&type=listen"><i class="fa fa-volume-up" aria-hidden="true"></i><span>Listen</span></a>
            </li>
        </ul>
    </div>
</div>




<?php
$answers = Magenest_Learn_Question::getAnswerByPostId($postId);
?>

    <span id="dummy"></span>

Turn on Vietnamse <input type="checkbox" data-bind="checked: showvn" />
<div id="learn-answer">
    <div data-bind="foreach:answers">
        <div class="tbr">
        <div class="vn tbc" data-bind="visible:turnvn"> <span data-bind="text:vn"></span></div>
        <div class ="en" tbc><input data-bind="value:enAnswer" type="text"></div>
        <div class ="check-answer tbc"><span data-bind="click:check"> Check</span> </div>
           <div>Vietnamse <input type="checkbox" data-bind="checked: turnvn" /></div>

            <div class ="check-answer right" data-bind="visible:correct" ><span > Right</span></div>
        <div class ="check-answer wrong" data-bind="visible:wrong"><span > Wrong</span></div>
        </div>

    </div>

</div>
<script type="text/javascript">
    function playSound(soundfile) {
      /*  document.getElementById("dummy").innerHTML=
            "<embed src=\""+soundfile+"\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";*/

        var audio = new Audio(soundfile);
        audio.play();
    }

    function compareStrings (string1, string2, ignoreCase, useLocale) {
        if (ignoreCase) {
            if (useLocale) {
                string1 = string1.toLocaleLowerCase();
                string2 = string2.toLocaleLowerCase();
            }
            else {
                string1 = string1.toLowerCase();

                if (string2 != undefined) {
                    string2 = string2.toLowerCase();
                } else {
                    return false;
                }

            }
        }

        return string1 === string2;
    }

    function Answer(data) {
        this.en = ko.observable(data.en);
        this.vn = ko.observable(data.vn);
        this.enAnswer = ko.observable();
        this.id = ko.observable(0);
        this.turnvn = ko.observable(false);

        this.correct = ko.observable(false);
        this.wrong = ko.observable();

        this.check = function(answer, event) {
            if (compareStrings(answer.en(),answer.enAnswer() , true, false) ) {

               // playSound('<?php echo LEARN_URL?>/audio/excellent__us_1.mp3');
              //  playSoundplaySound('<?php echo LEARN_URL?>/audio/' + answer.en() +'__us_1.mp3');
                //playSoundplaySound('<?php echo LEARN_URL?>/audio/' + answer.en() +'.mp3');
                answer.correct(true);
                answer.wrong(false);
            } else {
               // console.log(answer.en());
                //console.log(answer.enAnswer());
               // playSound('<?php echo LEARN_URL?>/audio/wrong__us_1.mp3');
                playSound('<?php echo LEARN_URL?>/audio/' + answer.en() +'.mp3');
                answer.correct(false);
                answer.wrong(true);


            }


        }
    }

    function ViewModel() {
        // Data
        var self = this;
        self.answers = ko.observableArray([]);
        self.newEn = ko.observable();
        self.newVn = ko.observable();
        self.newCorrect = ko.observable(true);
        self.answerCount = ko.computed(function() {
            return (self.answers().length);
        });

        self.showvn = ko.observable(false);

        // Operations
        self.addAnswer = function(answer, event) {

            event.cancelBubble = true;
            event.stopImmediatePropagation();
            self.answers.push(new Answer({ en: this.newEn(), vn:this.newVn(), correct: this.newCorrect() }));
            self.newEn("");
            self.newVn("");
            self.newCorrect(false);
        };
        self.removeAnswer = function(answer) { self.answers.destroy(answer) };
        self.save = function() {
            var answerSaveUrl = ajaxurl + '?action=learn_answer_save';

            jQuery.ajax(answerSaveUrl, {
                data: ko.toJSON({ tasks: self.tasks }),
                type: "post", contentType: "application/json",
                success: function(result) { alert(result) }
            });
        };

        // Load initial state from server, convert it to Task instances, then populate self.tasks
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';

        var answerUrl = ajaxurl + '?action=learn_answer' + '&post=<?php echo $postId?>';
        //console.log(answerUrl);
        jQuery.getJSON(answerUrl, function(allData) {
           // console.log(allData);
            var mappedTasks = jQuery.map(allData, function(item) { return new Answer(item) });
            self.answers(mappedTasks);
        });
    }

    ko.applyBindings(new ViewModel(), document.getElementById('learn-answer'));

</script>
</body>
</html>