$(document).ready(function() {
    var view = initializeView();
    getData();
    $(document).on('dataload', function(loadEvent) {
        var data = loadEvent.responseData;
        showData(view, data);
    }); 
});

function initializeView() {
    var view = $('#view')[0];
    styleCanvas(view);
    view.context = view.getContext('2d');
    return view;
}

function styleCanvas(canvas) {
    canvas.height = 500;
    canvas.width = 500;
    canvas.style.height = 500;
    canvas.style.width = 500;
    canvas.style['background-color'] = 'black';
    return null;
}

function getData() {
    $.getJSON('./anonymizeddataasJSON.php', function(responseData) {
        var loadEvent = $.Event('dataload');
        loadEvent.responseData = responseData;
        $(document).trigger(loadEvent);
    });
}

function showData(view, data) {
    var width = view.width,
        height = view.height;
    $.each(data, function(index, comparison) {
        // For each comparison, add a random x / y coordinate to the datapoint
        comparison.x = randint(0, width);
        comparison.y = randint(0, height);
    });
}

function randint(min, max) {
    // Returns an integer from min to max, not including max
    return Math.floor(Math.random() * max) - Math.floor(min);
}
