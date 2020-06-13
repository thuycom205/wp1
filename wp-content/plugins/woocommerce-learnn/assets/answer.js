function Answer(data) {
    this.en = ko.observable(data.en);
    this.vn = ko.observable(data.vn);
    this.id = ko.observable(0);

    this.correct = ko.observable(data.correct);
}

function ViewModel() {
    // Data
    var self = this;
    self.answers = ko.observableArray([]);
    self.newEn = ko.observable();
    self.newVn = ko.observable();
    self.newCorrect = ko.observable(true);
    self.answerCount = ko.computed(function () {
        return (self.answers().length);
    });


    self.vols = ko.observableArray([]);
    self.newEnVol = ko.observable();
    self.newVnVol = ko.observable();

    // Operations
    self.addAnswer = function (answer, event) {

        event.cancelBubble = true;
        event.stopImmediatePropagation();
        self.answers.push(new Answer({en: this.newEn(), vn: this.newVn(), correct: this.newCorrect()}));
        self.newEn("");
        self.newVn("");
        self.newCorrect(false);
    };
    self.removeAnswer = function (answer) {
        self.answers.destroy(answer)
    };
    self.save = function () {
        var answerSaveUrl = ajaxurl + '?action=learn_answer_save';

        jQuery.ajax(answerSaveUrl, {
            data: ko.toJSON({tasks: self.tasks}),
            type: "post", contentType: "application/json",
            success: function (result) {
                alert(result)
            }
        });
    };

    // Load initial state from server, convert it to Task instances, then populate self.tasks
   // if (postId != undefined) {
        var answerUrl = ajaxurl + '?action=learn_answer' + '&post=' + postId;
    console.log(answerUrl);
    jQuery.getJSON(answerUrl, function (allData) {
        console.log(allData);
        var mappedTasks = jQuery.map(allData, function (item) {
            return new Answer(item)
        });
        self.answers(mappedTasks);
    });
//}
}

ko.applyBindings(new ViewModel(), document.getElementById('learn-answer'));
