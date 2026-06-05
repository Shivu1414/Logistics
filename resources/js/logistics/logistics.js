let map;
let directionsService;
let directionsRenderer;

function initializeMap() {

    map = new google.maps.Map(
        document.getElementById('route_map'),
        {
            zoom: 5,
            center: {
                lat: 22.9734,
                lng: 78.6569
            }
        }
    );

    directionsService = new google.maps.DirectionsService();

    directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: false
    });

    directionsRenderer.setMap(map);
}

function drawRoute(origin, destination) {

    directionsService.route(
        {
            origin: {
                placeId: origin
            },

            destination: {
                placeId: destination
            },

            travelMode: google.maps.TravelMode.DRIVING
        },

        function (result, status) {

            if (status === 'OK') {

                directionsRenderer.setDirections(result);

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Map Error',
                    text: 'Unable to draw route'
                });

            }
        }
    );
}

$(document).ready(function () {
    initializeMap();
});

// City From Search
$(document).on('keyup', '#city_from', function () {

    let search = $(this).val();

    if (search.length < 2) {
        $('#city_from_dropdown').addClass('hidden');
        return;
    }

    $.ajax({
        url: '/city-suggestions',
        type: 'GET',
        data: {
            search: search
        },

        beforeSend: function () {

            $('#city_from_dropdown')
                .html(`
                    <div class="p-3 text-sm text-gray-500">
                        Loading...
                    </div>
                `)
                .removeClass('hidden');

        },

        success: function (response) {

            let html = '';

            if (response.cities.length > 0) {

                $.each(response.cities, function (index, city) {

                    html += `
                        <div
                            class="city-from-option px-4 py-3 cursor-pointer hover:bg-blue-50 border-b border-gray-100"
                            data-name="${city.description}"
                            data-place-id="${city.place_id}"
                        >
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                            ${city.description}
                        </div>
                    `;
                });

            } else {

                html = `
                    <div class="p-3 text-sm text-gray-500">
                        No cities found
                    </div>
                `;
            }

            $('#city_from_dropdown')
                .html(html)
                .removeClass('hidden');
        },

        error: function () {

            $('#city_from_dropdown')
                .html(`
                    <div class="p-3 text-sm text-red-500">
                        Failed to load cities
                    </div>
                `)
                .removeClass('hidden');
        }
    });

});


// City To Search
$(document).on('keyup', '#city_to', function () {

    let search = $(this).val();

    if (search.length < 2) {
        $('#city_to_dropdown').addClass('hidden');
        return;
    }

    $.ajax({
        url: '/city-suggestions',
        type: 'GET',
        data: {
            search: search
        },

        beforeSend: function () {

            $('#city_to_dropdown')
                .html(`
                    <div class="p-3 text-sm text-gray-500">
                        Loading...
                    </div>
                `)
                .removeClass('hidden');

        },

        success: function (response) {

            let html = '';

            if (response.cities.length > 0) {

                $.each(response.cities, function (index, city) {

                    html += `
                        <div
                            class="city-to-option px-4 py-3 cursor-pointer hover:bg-blue-50 border-b border-gray-100"
                            data-name="${city.description}"
                            data-place-id="${city.place_id}"
                        >
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                            ${city.description}
                        </div>
                    `;
                });

            } else {

                html = `
                    <div class="p-3 text-sm text-gray-500">
                        No cities found
                    </div>
                `;
            }

            $('#city_to_dropdown')
                .html(html)
                .removeClass('hidden');
        },

        error: function () {

            $('#city_to_dropdown')
                .html(`
                    <div class="p-3 text-sm text-red-500">
                        Failed to load cities
                    </div>
                `)
                .removeClass('hidden');
        }
    });

});


// Select City From
$(document).on('click', '.city-from-option', function () {

    $('#city_from')
        .val($(this).data('name'))
        .attr('data-place-id', $(this).data('place-id'));

    $('#city_from_dropdown').addClass('hidden');

});


$(document).on('click', '.city-to-option', function () {

    $('#city_to')
        .val($(this).data('name'))
        .attr('data-place-id', $(this).data('place-id'));

    $('#city_to_dropdown').addClass('hidden');

});


// Close Dropdowns
$(document).on('click', function (e) {

    if (!$(e.target).closest('#city_from, #city_from_dropdown').length) {
        $('#city_from_dropdown').addClass('hidden');
    }

    if (!$(e.target).closest('#city_to, #city_to_dropdown').length) {
        $('#city_to_dropdown').addClass('hidden');
    }

});




$(document).on('click', '#search_route', function () {

    let fromCity = $('#city_from').val().trim();
    let toCity = $('#city_to').val().trim();

    let originPlaceId = $('#city_from').attr('data-place-id');
    let destinationPlaceId = $('#city_to').attr('data-place-id');

    if (!fromCity || !toCity) {

        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please select both cities'
        });

        return;
    }

    if (!originPlaceId || !destinationPlaceId) {

        Swal.fire({
            icon: 'warning',
            title: 'Invalid Selection',
            text: 'Please select both cities from the suggestions list'
        });

        return;
    }

    $.ajax({
        url: '/route-metrics',
        type: 'POST',

        data: {
            origin_place_id: originPlaceId,
            destination_place_id: destinationPlaceId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },

        beforeSend: function () {

            $('#search_route')
                .prop('disabled', true)
                .html(`
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Searching...
                `);

        },

        success: function (response) {

            console.log(response);

            $('#route_result').html(`
                <div class="bg-white rounded-xl p-5 shadow-lg border border-gray-100 mt-4">
                    
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg text-gray-800">
                            Route Details
                        </h3>

                        <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                            Success
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">
                                Distance
                            </p>
                            <p class="text-xl font-semibold text-blue-700">
                                ${response.distance ?? 'N/A'}
                            </p>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">
                                Duration
                            </p>
                            <p class="text-xl font-semibold text-green-700">
                                ${response.duration ?? 'N/A'}
                            </p>
                        </div>

                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-gray-600">
                        <div>
                            <strong>From:</strong> ${fromCity}
                        </div>

                        <div>
                            <strong>To:</strong> ${toCity}
                        </div>
                    </div>

                </div>
            `);

            drawRoute(
                $('#city_from').attr('data-place-id'),
                $('#city_to').attr('data-place-id')
            );

        },

        error: function (xhr) {

            console.error(xhr);

            Swal.fire({
                icon: 'error',
                title: 'Route Search Failed',
                text: xhr.responseJSON?.message || 'Failed to fetch route details'
            });

        },

        complete: function () {

            $('#search_route')
                .prop('disabled', false)
                .html('Search');

        }
    });

});