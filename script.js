$(document).ready((oEvent) => {
    registerEventListeners();


    function registerEventListeners() {
        $(".submitLogin").on("click", function (oEvent) {
            console.log(this);
        });
        /*$('.signature-bold').on('click', function (data) {
            alert(getInputSelection($(".change-signature-textarea")));
        });*/
    }
    function getInputSelection(elem) {
        if (typeof elem != "undefined") {
            var text
            s = elem[0].selectionStart;
            e = elem[0].selectionEnd;
            console.log(elem.val());
            return elem.val().substring(s, e);
        } else {
            return '';
        }
    }
});
