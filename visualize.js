/*
$(document)
    .on('ready', loadData)
    .on('load.cfd_mds_data', initialize)
// Globals
var cfd_mds_raw_data,
    cfd_mds_data,
    faceLocations,
    participants,
    num_faces;
    function loadData() {
        password = prompt('password');
        $.post('./49f7cd18dc64f4c82563f45844202e3fa8b84653.php', {'password': password, 'page': 'datastore'})
            .success(parseDatastore);
    }
function parseDatastore(response) {
    function parseRow(row) {
        var cols = row.split('\t');
        parsedrow = _.object(['face1_id', 'face2_id', 'face1_url', 'face2_url', 'similarity', 'confirmation'], row.split('\t'));
        if(parsedrow['face1_url'] && parsedrow['face2_url']) {
            parsedrow['face1_race'] = parsedrow['face1_url'].split('~dma/')[1][0] == 'B' ? 'black' : 'white';
            parsedrow['face2_race'] = parsedrow['face2_url'].split('~dma/')[1][0] == 'B' ? 'black' : 'white';
            parsedrow['face1_gender'] = parsedrow['face1_url'].split('~dma/')[1][1] == 'M' ? 'male' : 'female';
            parsedrow['face2_gender'] = parsedrow['face2_url'].split('~dma/')[1][1] == 'M' ? 'male' : 'female';
        }
        return parsedrow;
    }
    var rows = $(response).text().split('\n');
    cfd_mds_raw_data = rows.map(parseRow);
    console.log(cfd_mds_raw_data);
    $.post('./49f7cd18dc64f4c82563f45844202e3fa8b84653.php', {'password': password, 'page': 'participants'})
        .success(mergeParticipantsWithDatastore);
    delete password;
    return cfd_mds_raw_data;
}
function mergeParticipantsWithDatastore(response) {
    function parseRow(row) {
        var cols = row.split('\t');
        return _.object(['confirmation', 'participant_age',  'participant_gender', 'participant_zip', 'participant_race'], cols);
    }
    var rows = $(response).text().split('\n');
    var parsedrows = rows.map(parseRow);
    participants = _.object(_.pluck(parsedrows, 'confirmation'), parsedrows);
    function mergeDataAndParticipants(datarow, i) {
        // Use the confrimation key to merge them
        if(datarow) {
            var confirmation = datarow['confirmation'];
            return _.extend(datarow, participants[confirmation]);
        }
    }
    cfd_mds_raw_data = cfd_mds_raw_data.map(mergeDataAndParticipants);
    setFaceLocations();
    return cfd_mds_raw_data;
}
function setFaceLocations() {
    faceLocations = {};
    var face_ids = _.pluck(cfd_mds_raw_data, 'face1_id').map(toInt);
    var face_urls = _.pluck(cfd_mds_raw_data, 'face1_url');
    num_faces = _.max(face_ids) + 1;
    cfd_mds_raw_data.forEach(function(row) {
        var face1_id = row['face1_id'],
            face1_url = row['face1_url'],
            similarity = row['similarity'],
            confirmation = row['confirmation'];
        if(face1_id >= 0 && face1_id < num_faces) {
            faceLocations[face1_id] = face1_url;
        }
    });
    var loadEvent = $.Event('load.cfd_mds_data', {'cfd_mds_data': cfd_mds_raw_data});
    $(document).trigger(loadEvent);
    return faceLocations;
}
function initialize(Event) {
    // Note that cfd_mds_data is global, as is num_faces
    // cfd_mds_raw data will eventually be filtered.
    dataFilter = function(row) {return true}
    cfd_mds_data = cfd_mds_raw_data.filter(dataFilter);
    $('#show-general-information').on('click', showGeneralInformation);
    $('#show-similarity-heatmap').on('click', showSimilarityHeatmap);
    $('#show-classical-mds').on('click', showClassicalMDS);
    showGeneralInformation();
}

// Heat Map
function showSimilarityHeatmap() {
    var faces = _.range(num_faces);
    var grid = faces.map(function() {return faces.map(function() {return [];});});
    cfd_mds_data.forEach(function(row) {
        var face1_id = row['face1_id'],
        face2_id = row['face2_id'],
        face1_url = row['face1_url'],
        face2_url = row['face2_url'],
        similarity = row['similarity'];
        included = true;
        if(face1_id >= 0 && face1_id < num_faces && face2_id >= 0 && face2_id < num_faces) {
            grid[toInt(row['face1_id'])][toInt(row['face2_id'])].push(toInt(row['similarity']));
            faceLocations[face1_id] = face1_url;
            faceLocations[face2_id] = face2_url;
        }
    });
    $('#view').html('<h2>Heat Map</h2>');
    $('#view').append('<p>A heat map of how similar each pair of faces is. The face on the left is represented by the horizontal position and the face on the right is represented by the vertical axis. Click a cell on the diagram to look at the pair of faces a cell represents and what the ratings of that face pair were.</p>');
    var heatmap = document.createElement('canvas');
    $('#view').append(heatmap);
    $(heatmap).addClass('heatmap');
    heatmap.width = $('#view').width();
    heatmap.height = $('#view').width();
    heatmap.style.width = $('#view').width();
    heatmap.style.height = $('#view').width();
    var context = heatmap.getContext('2d');
    var cellsize = (heatmap.width / num_faces) | 0;
    for(var f1 = 0; f1 < num_faces; f1++) {
        for(var f2 = 0; f2 < num_faces; f2++) {
            try {
                context.fillStyle = colorFromSimilarity(average(grid[f1][f2]))
                context.fillRect(f1 * cellsize, f2 * cellsize, cellsize, cellsize);
            }
            catch(e) {
                console.log(e);
            }
        }
    }
    function handleClick(Event) {
        var x = Event.offsetX; 
        var y = Event.offsetY;
        var f1 = Event.offsetX / cellsize | 0;
        var f2 = Event.offsetY / cellsize | 0;
        showComparisonViewer(faceLocations[f1], faceLocations[f2])
    }
    $(heatmap).on('click', handleClick);
}
function showComparisonViewer(face1_url, face2_url) {
    $('#comparison-viewer')
        .show()
        .html('')
        .append($('<img src="'+face1_url+'"/>'))
        .append($('<img src="'+face2_url+'"/>'))
        .append($('<div>Click to dismiss</div>'))
        .on('click', hideComparisonViewer);
}
function hideComparisonViewer() {
    $('#comparison-viewer').hide();
}
function colorFromSimilarity(similarity) {
    if(similarity === null) return '#7777cc';
    else {
        var r = (32 * (7 - similarity)).toString(16);
        var g = (32 * (similarity)).toString(16);
        var b = '00';
        if(r.length < 2) r = '0' + r;
        if(r.length > 2) r = 'FF';
        if(g.length < 2) g = '0' + r;
        if(g.length > 2) g = 'FF';
        if(b.length < 2) b = '0' + r;
        if(b.length > 2) b = 'FF';
        var color = '#' + g + g + g;
        return color;
    }
}

// Organized Heat Map
function showOrderedSimilarityHeatmap() {
    function getClassicalMDSData() {
        // Get the data from the python script
        $.get('./2dlocations.txt')
            .success(parseLocations);
    }
    function parseLocations(response) {
        
    }
    var grid = faces.map(function() {return faces.map(function() {return [];});});
    cfd_mds_data.forEach(function(row) {
        var face1_id = row['face1_id'],
        face2_id = row['face2_id'],
        face1_url = row['face1_url'],
        face2_url = row['face2_url'],
        similarity = row['similarity'];
        included = true;
        if(face1_id >= 0 && face1_id < num_faces && face2_id >= 0 && face2_id < num_faces) {
            grid[toInt(row['face1_id'])][toInt(row['face2_id'])].push(toInt(row['similarity']));
            faceLocations[face1_id] = face1_url;
            faceLocations[face2_id] = face2_url;
        }
    });
}

// Classical MDS
function showClassicalMDS() {
    function getClassicalMDSData() {
        // Get the data from the python script
        $.get('./2dlocations.txt')
            .success(showClassicalMDSData);
    }
    function showClassicalMDSData(rawData) {
        var data = rawData.split('\n').map(
        function(row) {
            return _.object(['src', 'x', 'y'], row.split('\t'));    
        });
        $('#view').html('<h2>Results of Classical Multidimensional Scaling</h2>');
        $graph = $('<div/>')
            .addClass('graph')
            .width($('#view').width() * 0.8)
            .height($('#view').width() * 0.8)
            .appendTo('#view');
        data.forEach(
        function(image) {
            $('<img/>')
                .attr('src', image['src'].replace(/"/g, ""))
                .addClass('plotpoint')
                .css({
                    'top': $graph.height() / 2 + image['y'] * 100,
                    'left': $graph.width() / 2 + image['x'] * 100,
                })
                .appendTo($graph);
        });
    //{
    }
    getClassicalMDSData();
}

// General Information
function showGeneralInformation() {
    $('#view').html($('#general-information').html());
    var avgsim = average(_.pluck(cfd_mds_data, 'similarity').map(toInt));
    $('.average-similarity').html((100 * avgsim | 0) / 100);
    var avgage = average(_.pluck(cfd_mds_data, 'participant_age').map(toInt));
    $('.average-age').html((1 * avgage | 0) / 1);
    console.log(participants)
    $('.number-of-participants').html(_.keys(participants).length);
    $('.number-of-comparisons').html(cfd_mds_data.length);
}

// Utility functions
function toInt(i) { return i | 0; }
function average(list) {
    if(!list.length) return null;
    return list.reduce(function(a,b){return a+b})/list.length;
}

*/
// Functions starting with _p_ are promises
function _p_getData() {
    var deferred = new $.Deferred();
    var promise = deferred.promise();
    var cfd_mds_data = getDataFromLocalStorage();
    if(cfd_mds_data) {
        deferred.resolve(cfd_mds_data);
    }
    else {
        
    }
    return promise;
}
function getDataFromLocalStorage() {
    var cfd_mds_data = JSON.parse(localStorage.getItem("cfd_mds_data"));
    return cfd_mds_data;
}
function _p_getPassord() {
    var deferred = new $.Deferred();
    var promise = deferred.promise();
    var $pwform = $('<form class="password-holder"/>');
    var $label = $('<label for="password">Enter your password</label>');
    var $input = $('<input type="password" id="password"/>');
    var $submit = $('<div><button type="submit">Save Password</button></div>');
    $pwform
        .append($label)
        .append($input)
        .append($submit)
        .appendTo($("body"))
        .css({
            "z-index": "1",
            "background": "white",
            "height": "10em",
            "width": "25em",
        });
    $pwform.submit(function(e) {
        e.preventDefault();
        console.log($input.val());
        deferred.resolve($input.val());
    })
    return promise;
}
