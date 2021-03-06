// Generated by CoffeeScript 1.4.0
(function() {

  $(function() {
    var add_event_coords, addition_editing_panel, address_timeout, category, coords, edit_data, event_coords, events_stream, map_cursor, panel, put_events_coords, reset_options, time, time_interval, time_limit, timeout, uploader, visible;
    panel = $('.cs-home-add-panel');
    events_stream = $('.cs-home-events-stream');
    category = 0;
    visible = 0;
    time = 1;
    time_interval = 86400;
    time_limit = 1;
    timeout = time * time_interval * time_limit;
    coords = [0, 0];
    event_coords = null;
    put_events_coords = false;
    map_cursor = null;
    edit_data = 0;
    address_timeout = 0;
    uploader = null;
    reset_options = function() {
      visible = 0;
      time = 1;
      time_interval = 86400;
      time_limit = 1;
      timeout = time * time_interval * time_limit;
      coords = [0, 0];
      event_coords && map.geoObjects.remove(event_coords);
      event_coords = null;
      map_cursor && map_cursor.remove();
      map_cursor = null;
      put_events_coords = false;
      uploader && uploader.destroy();
      return uploader = null;
    };
    $(document).on('click', '.cs-home-add, .cs-home-add-close', function() {
      reset_options();
      events_stream.toggle('fast');
      return panel.html('').toggle('fast', function() {
        var content;
        if (panel.css('display') !== 'none') {
          if ($('.cs-home-settings-panel, .cs-home-settings-coordinator').css('width') !== '0px') {
            $('.cs-home-settings').click();
          }
          content = $('.cs-home-filter-category').html();
          panel.html("<ul>" + content + "</ul>");
          if (cs.home.automaidan) {
            return panel.find('li').each(function() {
              if ($.inArray($(this).data('id'), [1, 3, 6, 7, 8, 17, 21, 22]) === -1) {
                return $(this).hide();
              }
            });
          }
        }
      });
    });
    add_event_coords = function(point) {
      coords = point;
      event_coords && map.geoObjects.remove(event_coords);
      event_coords = new ymaps.Placemark(coords, {}, {
        draggable: true,
        iconLayout: 'default#image',
        iconImageHref: '/components/modules/Home/includes/img/new-event.png',
        iconImageSize: [91, 86],
        iconImageOffset: [-36, -86],
        iconImageShape: new ymaps.shape.Polygon(new ymaps.geometry.pixel.Polygon([[[35 - 36, 85 - 86], [65 - 36, 55 - 86], [71 - 36, 43 - 86], [72 - 36, 31 - 86], [64 - 36, 13 - 86], [53 - 36, 4 - 86], [37 - 36, 0 - 86], [22 - 36, 2 - 86], [11 - 36, 10 - 86], [3 - 36, 22 - 86], [0 - 36, 35 - 86], [3 - 36, 51 - 86], [35 - 36, 85 - 86]]]))
      });
      map.geoObjects.add(event_coords);
      return event_coords.events.add('geometrychange', function(e) {
        return coords = e.get('originalEvent').originalEvent.newCoordinates;
      });
    };
    (function() {
      var map_init;
      map_init = setInterval((function() {
        if (!window.map || !map.events) {
          return;
        }
        clearInterval(map_init);
        map.events.add('click', function(e) {
          if (!put_events_coords) {
            return;
          }
          return add_event_coords(e.get('coords'));
        });
      }), 100);
    })();
    addition_editing_panel = function() {
      var $this, content, edit, name, submit;
      $this = $(this);
      edit = $this.hasClass('cs-home-edit');
      if (edit) {
        submit = "<button class=\"cs-home-edit-process\">Зберегти</button>";
        name = cs.home.categories[edit_data.category].name;
      } else {
        category = $this.data('id');
        submit = "<button class=\"cs-home-add-process\">Додати</button>";
        name = $this.find('span').text();
      }
      content = ("<h2>" + name + "</h2>\n<textarea placeholder=\"Коментар\"></textarea>\n<button class=\"cs-home-add-image-button uk-icon-picture-o\"> Додати фото</button>") + ("<h3>Актуально протягом</h3>\n<div data-uk-dropdown=\"{mode:'click'}\" class=\"uk-button-dropdown\">\n	<button type=\"button\" class=\"uk-button\">\n		<span class=\"uk-icon-caret-down\"></span> <span>Вказаного часу</span>\n	</button>\n	<div class=\"uk-dropdown\">\n		<ul class=\"cs-home-add-time-limit uk-nav uk-nav-dropdown\">\n			<li class=\"uk-nav-header\">Актуально протягом</li>\n			<li data-id=\"1\">\n				<a>Вказаного часу</a>\n			</li>\n			<li data-id=\"0\">\n				<a>Без обмежень</a>\n			</li>\n		</ul>\n	</div>\n</div>\n<div class=\"cs-home-actuality-control\">\n	<input class=\"cs-home-add-time\" type=\"number\" min=\"1\" value=\"1\"/>\n	<div data-uk-dropdown=\"{mode:'click'}\" class=\"uk-button-dropdown\">\n		<button type=\"button\" class=\"uk-button\">\n			<span class=\"uk-icon-caret-down\"></span> <span>Днів</span>\n		</button>\n		<div class=\"uk-dropdown\">\n			<ul class=\"cs-home-add-time-interval uk-nav uk-nav-dropdown\">\n				<li class=\"uk-nav-header\">Одиниці часу</li>\n				<li data-id=\"60\">\n					<a>Хвилин</a>\n				</li>\n				<li data-id=\"3600\">\n					<a>Годин</a>\n				</li>\n				<li data-id=\"86400\">\n					<a>Днів</a>\n				</li>\n			</ul>\n		</div>\n	</div>\n</div>\n<input type=\"text\" class=\"cs-home-add-location-address\" placeholder=\"Адреса або точка на карті\">\n<button class=\"cs-home-add-location uk-icon-location-arrow\"></button>\n<p class=\"help\">не забувайте вказувати місто для точного пошуку, і перевіряйте де помістилась точка</p>\n<div>\n	<button class=\"cs-home-add-close uk-icon-times\"></button>\n	" + submit + "\n</div>");
      panel.html(content);
      put_events_coords = true;
      map_cursor = map.cursors.push('pointer');
      (function() {
        var uploader_button;
        uploader_button = $('.cs-home-add-image-button');
        return uploader = cs.file_upload(uploader_button, function(files) {
          if (files.length) {
            uploader_button.next('img').remove();
            return uploader_button.after("<img src=\"" + files[0] + "\" alt=\"\" class=\"cs-home-add-image\">");
          }
        });
      })();
      if (edit) {
        $(".cs-home-add-visible [data-id=" + edit_data.visible + "]").click();
        $('.cs-home-add-time-limit [data-id=' + (edit_data.timeout > 0 ? 1 : 0) + ']').click();
        $(".cs-home-add-time").val(edit_data.time).change();
        $(".cs-home-add-time-interval [data-id=" + edit_data.time_interval + "]").click();
        panel.find('textarea').val(edit_data.text);
        add_event_coords([edit_data.lat, edit_data.lng]);
        if (edit_data.img) {
          return $('.cs-home-add-image-button').after("<img src=\"" + edit_data.img + "\" alt=\"\" class=\"cs-home-add-image\">");
        }
      } else if (category === 1) {
        $(".cs-home-add-time").val(2).change();
        return $(".cs-home-add-time-interval [data-id=3600]").click();
      }
    };
    panel.on('click', '> ul > li', addition_editing_panel).on('click', '.cs-home-add-visible [data-id]', function() {
      var $this;
      $this = $(this);
      visible = $this.data('id');
      return $this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text());
    }).on('click', '.cs-home-add-time-limit [data-id]', function() {
      var $this;
      $this = $(this);
      time_limit = $this.data('id');
      timeout = time * time_interval * time_limit;
      $('.cs-home-actuality-control')[time_limit ? 'show' : 'hide']();
      return $this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text());
    }).on('click', '.cs-home-add-time-interval [data-id]', function() {
      var $this;
      $this = $(this);
      time_interval = $this.data('id');
      timeout = $('.cs-home-add-time').val() * time_interval * time_limit;
      return $this.parentsUntil('[data-uk-dropdown]').prev().find('span:last').html($this.find('a').text());
    }).on('change', '.cs-home-add-time', function() {
      var $this;
      $this = $(this);
      return timeout = time_interval * $this.val() * time_limit;
    }).on('click', '.cs-home-add-location', function() {
      return alert('Клікніть місце з подією на карті');
    }).on('click', '.cs-home-add-process', function() {
      var comment, img;
      if (category && coords[0] && coords[1]) {
        comment = panel.find('textarea').val();
        img = panel.find('.cs-home-add-image');
        return ymaps.geocode(coords, {
          json: true,
          results: 1
        }).then(function(res) {
          var address_details;
          address_details = res.GeoObjectCollection.featureMember;
          if (address_details.length) {
            address_details = address_details[0].GeoObject.metaDataProperty.GeocoderMetaData.text;
          } else {
            address_details = '';
          }
          return $.ajax({
            url: 'api/Home/events',
            type: 'post',
            data: {
              category: category,
              time: time,
              time_interval: time_interval,
              timeout: timeout,
              lat: coords[0],
              lng: coords[1],
              visible: visible,
              text: comment,
              img: img.length ? img.attr('src') : '',
              address_details: address_details
            },
            success: function() {
              panel.hide('fast');
              events_stream.toggle('show');
              map.geoObjects.remove(event_coords);
              event_coords = null;
              put_events_coords = false;
              map_cursor.remove();
              map.update_events();
              return alert('Успішно додано, дякуємо вам!');
            }
          });
        });
      } else {
        return alert('Вкажіть точку на карті');
      }
    }).on('click', '.cs-home-edit-process', function() {
      var comment, img;
      if (coords[0] && coords[1]) {
        comment = panel.find('textarea').val();
        img = panel.find('.cs-home-add-image');
        return ymaps.geocode(coords, {
          json: true,
          results: 1
        }).then(function(res) {
          var address_details;
          address_details = res.GeoObjectCollection.featureMember;
          if (address_details.length) {
            address_details = address_details[0].GeoObject.metaDataProperty.GeocoderMetaData.text;
          } else {
            address_details = '';
          }
          return $.ajax({
            url: "api/Home/events/" + edit_data.id,
            type: 'put',
            data: {
              time: time,
              time_interval: time_interval,
              timeout: timeout,
              lat: coords[0],
              lng: coords[1],
              visible: visible,
              text: comment,
              img: img.length ? img.attr('src') : '',
              address_details: address_details
            },
            success: function() {
              panel.hide('fast');
              events_stream.toggle('show');
              map.geoObjects.remove(event_coords);
              event_coords = null;
              put_events_coords = false;
              map_cursor.remove();
              map.update_events();
              return alert('Успішно відредаговано!');
            }
          });
        });
      } else {
        return alert('Вкажіть точку на карті');
      }
    }).on('keyup change', '.cs-home-add-location-address', function() {
      var $this;
      $this = $(this);
      if ($this.val().length < 4) {
        return;
      }
      clearTimeout(address_timeout);
      return address_timeout = setTimeout((function() {
        return ymaps.geocode($this.val()).then(function(res) {
          coords = res.geoObjects.get(0).geometry.getCoordinates();
          map.panTo(coords, {
            fly: true,
            checkZoomRange: true
          }).then(function() {
            return map.zoomRange.get(coords).then(function(zoomRange) {
              return map.setZoom(zoomRange[1], {
                duration: 500
              });
            });
          });
          return add_event_coords(coords);
        });
      }), 300);
    });
    return $('#map').on('click', '.cs-home-edit', function() {
      var item;
      item = this;
      return $.ajax({
        url: 'api/Home/events/' + $(this).data('id'),
        type: 'get',
        success: function(data) {
          window.map.balloon.close();
          edit_data = data;
          reset_options();
          panel.show('fast');
          events_stream.hide('show');
          return addition_editing_panel.call(item);
        }
      });
    });
  });

}).call(this);
