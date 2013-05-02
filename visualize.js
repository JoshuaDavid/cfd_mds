$(document)
    .on('ready', loadData)
    .on('load.cfd_mds_data', initialize)
    function loadData() {
        $.post('./49f7cd18dc64f4c82563f45844202e3fa8b84653.php')
            .success(parseResponse);
    }
function parseResponse(response) {
    function parseRow(row) {
        var cols = row.split('\t');
        return _.object(['face1_id', 'face2_id', 'face1_url', 'face2_url', 'similarity'], row.split('\t'));
    }
    var rows = $(response).text().split('\n');
    var cfd_mds_data = rows.map(parseRow);
    var loadEvent = $.Event('load.cfd_mds_data', {'cfd_mds_data': cfd_mds_data});
    $(document).trigger(loadEvent);
    return cfd_mds_data;
}
function initialize(Event) {
    // Note that cfd_mds_data is global, as is num_faces
    cfd_mds_raw_data = Event.cfd_mds_data;
    // cfd_mds_raw data will eventually be filtered.
    cfd_mds_data = cfd_mds_raw_data;
    var face_ids = _.pluck(cfd_mds_data, 'face1_id').map(toInt);
    var face_urls = _.pluck(cfd_mds_data, 'face1_url');
    num_faces = _.max(face_ids) + 1;
    faceLocations = {};
    cfd_mds_data.forEach(function(row) {
        var face1_id = row['face1_id'],
        face2_id = row['face2_id'],
        face1_url = row['face1_url'],
        face2_url = row['face2_url'],
        similarity = row['similarity'];
        if(face1_id >= 0 && face1_id < num_faces && face2_id >= 0 && face2_id < num_faces) {
            faceLocations[face1_id] = face1_url;
            faceLocations[face2_id] = face2_url;
        }
    });
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

// Classical MDS
function showClassicalMDS() {
    function getClassicalMDSData() {
        // Get the data from the python script
        $.get('./2dlocations.txt')
            .success(showClassicalMDSData);
    }
    function showClassicalMDSData(rawData) {
        var data = rawData.split('\n').map(function(row) {
            return _.object(['src', 'x', 'y'], row.split('\t'));    
        });
        $('#view').html('<h2>Results of Classical Multidimensional Scaling</h2>');
        $graph = $('<div/>')
            .addClass('graph')
            .width($('#view').width() * 0.8)
            .height($('#view').width() * 0.8)
            .appendTo('#view');
        data.forEach(function(image) {
            $('<img/>')
                .attr('src', image['src'].replace(/"/g, ""))
                .addClass('plotpoint')
                .css({
                    'top': $graph.height() / 2 + image['y'] * 100,
                    'left': $graph.width() / 2 + image['x'] * 100,
                })
                .appendTo($graph);
        });
    }
    getClassicalMDSData();
}

// General Information
function showGeneralInformation() {
    $('#view').html($('#general-information').html());
    var avg = average(_.pluck(cfd_mds_data, 'similarity').map(toInt));
    $('.average-similarity').html((100 * avg | 0) / 100);
}

// Utility functions
function toInt(i) { return i | 0; }
function average(list) {
    if(!list.length) return null;
    return list.reduce(function(a,b){return a+b})/list.length;
}
