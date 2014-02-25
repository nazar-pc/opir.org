// Generated by CoffeeScript 1.4.0
(function() {

  $(function() {
    if (cs.module !== 'Streams') {
      return;
    }
    return ymaps.ready(function() {
      var add_streams_on_map, clusterer, filter_streams, icons_shape, placemarks, streams_cache;
      window.map = new ymaps.Map('map', {
        center: [50.45, 30.523611],
        zoom: 13,
        controls: ['typeSelector', 'zoomControl', 'fullscreenControl']
      });
      clusterer = new ymaps.Clusterer();
      clusterer.createCluster = function(center, geoObjects) {
        var cluster;
        cluster = ymaps.Clusterer.prototype.createCluster.call(this, center, geoObjects);
        cluster.options.set({
          icons: [
            {
              href: '/components/modules/Home/includes/img/cluster-46.png',
              size: [46, 46],
              offset: [-23, -23]
            }, {
              href: '/components/modules/Home/includes/img/cluster-58.png',
              size: [58, 58],
              offset: [-27, -27]
            }
          ]
        });
        return cluster;
      };
      map.geoObjects.add(clusterer);
      filter_streams = function(events) {
        var categories;
        categories = $('.cs-home-filter-category .active');
        return events.filter(function(event) {
          return !categories.length || categories.filter("[data-id=" + event.category + "]").length;
        });
      };
      placemarks = [];
      icons_shape = new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([[[23 - 24, 56 - 58], [44 - 24, 34 - 58], [47 - 24, 23 - 58], [45 - 24, 14 - 58], [40 - 24, 7 - 58], [29 - 24, 0 - 58], [17 - 24, 0 - 58], [7 - 24, 6 - 58], [0 - 24, 18 - 58], [0 - 24, 28 - 58], [4 - 24, 36 - 58], [23 - 24, 56 - 58]]]));
      add_streams_on_map = function(streams) {
        var stream;
        placemarks = [];
        for (stream in streams) {
          stream = streams[stream];
          placemarks.push(new ymaps.Placemark([stream.lat, stream.lng], {
            balloonContentBody: "<p><iframe width=\"260\" height=\"240\" src=\"" + stream.stream_url + "\" frameborder=\"0\" scrolling=\"no\"></iframe></p>"
          }, {
            hasHint: false,
            iconLayout: 'default#image',
            iconImageHref: '/components/modules/Home/includes/img/events.png',
            iconImageSize: [59, 56],
            iconImageOffset: [-24, -56],
            iconImageClipRect: [[0, 56 * (28 - 1)], [59, 56 * 28]],
            iconImageShape: icons_shape
          }));
        }
        clusterer.removeAll();
        return clusterer.add(placemarks);
      };
      streams_cache = [];
      $.ajax({
        url: 'api/Streams/streams',
        type: 'get',
        success: function(streams) {
          streams_cache = streams;
          add_streams_on_map(streams);
        },
        error: function() {}
      });
    });
  });

}).call(this);