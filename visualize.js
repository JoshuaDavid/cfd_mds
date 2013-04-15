REPULSION = 5000;
ATTRACTION = 1;
DAMPING = 0.9

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
    view.showPoint
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
        height = view.height,
        images = imagesFromData(data);
    $.each(images, function(index, image) {
        image.x = randint(0.00 * width, 0.99 * width);
        image.y = randint(0.00 * height, 0.99 * height);
        image.dx = 0;
        image.dy = 0;
    });
    function next() {
        console.log(images[0]);
        nextFrame(view, images);
        setTimeout(next, 50);
    }
    next();
}

function nextFrame(view, images) {
    view.context.fillStyle = 'black';
    view.context.fillRect(0, 0, view.width, view.height);
    view.context.fillStyle = 'white';
    $.each(images, function(image1id, image1) {
        $.each(image1['similarityTo'], function(image2id, similarities) {
            var similarity = Math.average(similarities);
            calculateForces(images[image1id], images[image2id], similarity);
        });
        image1.x += image1.dx;
        image1.y += image1.dy;
        view.context.fillRect(image1.x, image1.y, 2, 2);
    });
}


function calculateForces(image1, image2, similarity) {
    var distance2 = Math.pow(image2.x - image1.x, 2) + Math.pow(image2.y - image1.y, 2),
        distance = Math.sqrt(distance2),
        theta = Math.atan2(image2.x - image1.x, image2.y - image1.y);
    // All nodes are repelled from one another with a force inversely
    // proportional to the distance between them.
    image1.dx -= Math.sin(theta) * REPULSION / distance2;
    image1.dy -= Math.cos(theta) * REPULSION / distance2;
    image2.dx += Math.sin(theta) * REPULSION / distance2;
    image2.dy += Math.cos(theta) * REPULSION / distance2;

    image1.dx += Math.sin(theta) * similarity * ATTRACTION / distance;
    image1.dy += Math.cos(theta) * similarity * ATTRACTION / distance;
    image2.dx -= Math.sin(theta) * similarity * ATTRACTION / distance;
    image2.dy -= Math.cos(theta) * similarity * ATTRACTION / distance;

    image1.dx = constrain(image1.dx * DAMPING, -100, 100);
    image1.dy = constrain(image1.dy * DAMPING, -100, 100);
    image2.dx = constrain(image2.dx * DAMPING, -100, 100);
    image2.dy = constrain(image2.dy * DAMPING, -100, 100);

}

function imagesFromData(data) {
    var images = {};
    $.each(data, function(index, comparison) {
        var face1_id = comparison.face1_id,
            face2_id = comparison.face2_id,
            face1_url = comparison.face1_url,
            face2_url = comparison.face2_url,
            similarity = comparison.similarity;
        if(!images[face1_id]) {
            images[face1_id] = {};
            images[face1_id]['url'] = face1_url;
            images[face1_id]['similarityTo'] = {};
        }
        if(!images[face2_id]) {
            images[face2_id] = {};
            images[face2_id]['url'] = face2_url;
            images[face2_id]['similarityTo'] = {};
        }
        if(!images[face1_id]['similarityTo'][face2_id]) {
            images[face1_id]['similarityTo'][face2_id] = [];
        }
        images[face1_id]['similarityTo'][face2_id].push(similarity);
        if(!images[face2_id]['similarityTo'][face1_id]) {
            images[face2_id]['similarityTo'][face1_id] = [];
        }
        images[face2_id]['similarityTo'][face1_id].push(similarity);
    });
    return images;
}

function randint(min, max) {
    // Returns an integer from min to max, not including max
    return Math.floor(Math.random() * max) - Math.floor(min);
}

Math.average = function(iterable) {
    var sum = 0, numElements = 0;
    for(var i in iterable) {
        if(iterable.hasOwnProperty(i)) {
            sum += iterable[i];
            numElements += 1;
        }
    }
    return sum / numElements;
}

function constrain(value, min, max) {
    if(value < min) return min;
    else if(value > max) return max;
    else return value;
}
