/*!
 *
 * Script para verificar estado de los webservices
 *
 * Author: Javier Mendoza
 * Website: ITHB
 *
 *
 */

(function ($) {
  $.wssPlugin = function (options) {
    var defaults = {url:''},
    plugin = this,
    options = options || {};
    plugin.init = function () {
      var settings = $.extend({}, defaults, options);
      $.data(document, 'wssPlugin', settings);
    }
    plugin.init();
  }
  $.wssPlugin.init = function (callback) {
     var param=$.data(document, 'wssPlugin');
     $.ajax({
       url: param.url,
       dataType: 'html',
       async: false,
     })
     .done(function(data) {
       callback(data);
     });
  }
}(jQuery));
