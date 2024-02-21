document.addEventListener("DOMContentLoaded", function() {

    var redRange = document.getElementById("red");
    var greenRange = document.getElementById("green");
    var blueRange = document.getElementById("blue");

    function updateBackgroundColor() {
        var redValue = redRange.value;
        var greenValue = greenRange.value;
        var blueValue = blueRange.value;
        var backgroundColor = "rgb(" + redValue + "," + greenValue + "," + blueValue + ")";
        document.documentElement.style.backgroundColor = backgroundColor;
        document.body.style.backgroundColor = backgroundColor;
    }

    redRange.addEventListener("input", updateBackgroundColor);
    greenRange.addEventListener("input", updateBackgroundColor);
    blueRange.addEventListener("input", updateBackgroundColor);

    updateBackgroundColor();
});
