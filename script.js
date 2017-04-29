$(document).ready((oEvent) => {
    registerEventListeners();
    
    
    function registerEventListeners() {
        $(".submitLogin").on("click", function(oEvent) {
            console.log(this);
        })
    }
});