
Map = (function ($) {
  var Mortes = {};

  function initialize(element, svgPath) {
    d3.xml(svgPath, 'image/svg+xml', function (xml) {
      element.html(xml.documentElement);

      _setupCallbacks();
      _loadEstupros(function () {
        var focusedElementSlug = window.location.hash.replace('#', '');
        // Use Soledade as default
        if (focusedElementSlug == '') { focusedElementSlug = 'soledade'; };
        _focusInto(focusedElementSlug);
        _drawBars();
        _colorRegions();
      });
    });
  };

  function _setupCallbacks() {
    d3.selectAll('path')
    .on('mouseover', _hoverRegion)
    .on('click', _selectRegion);
  };

  function _hoverRegion() {
    _classOnlyThisAs(this.id, 'hover');
  };

  function _classOnlyThisAs(id, className) {
    d3.selectAll('.'+className).classed(className, false);
    d3.selectAll('.'+id).classed(className, true);
  };

  function _selectRegion() {
    var id = this.id,
    codigo = id.replace(/.*_/, '');

    _classOnlyThisAs(id, 'active');
    _draw_timeline(codigo);
    _showInfo(codigo);
    window.location.hash = $.slug(Mortes[codigo].nome);
  };

  function _showInfo(codigo) {
    var cidade = Mortes[codigo];
    if (!cidade) { return; }

    var day = Math.round(100 * cidade.pela_manha / cidade.ocorrencias),
    night = 100 - day,
    ranking = _keysSortedByOpacity().indexOf(codigo) + 1,
    proporcao = cidade.proporcao,
    home = Math.round((100 * cidade.local.residencia) / cidade.ocorrencias),
    street = Math.round((100 * cidade.local.via_publica) / cidade.ocorrencias),
    others = 100 - home - street;

    $('#info h3').text(cidade.nome);
    $('.population em').text(_formatNumber(cidade.populacao));
    $('.victim em').text(cidade.media_idade_vitima);
    $('.author em').text(cidade.media_idade_autor);
    $('.night em').text(night+'%');
    $('.day em').text(day+'%');
    $('.home em').text(home+'%');
    $('.street em').text(street+'%');
    $('.others em').text(others+'%');
    $('.ranking em').text(ranking+'ª');
    $('.proporcao em').text(proporcao);
  };

  function _formatNumber(number) {
    return number.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  };

  function _loadEstupros(callback) {
    $.getJSON('../data/dados_estupros2.json', function (data) {
      Mortes = data;
      callback();
    });
  };

  function _focusInto(slug) {
    var element;

    for (id in Mortes) {
      if ($.slug(Mortes[id].nome) == slug) {
        element = document.getElementById('svg_'+id);
        break;
      }
    }

    if (!element) { return; }

    d3.select(element).on('click').call(element);
  };

  function _colorRegions() {
    //console.log(o.ordinal(10));
    $("#svg_4305108").css("fill", "rgba(153,0,0,0.2)");
    $("#svg_4302105").css("fill", "rgba(153,0,0,0.1)");
    d3.selectAll('.bar-graph li')
    .each(function (id) {
      var d3RegionMap = d3.select('path.'+this.classList[0]),
      opacity = d3RegionMap.attr('style').replace(/.*: (.*);?/, '$1'),
      span = d3.select(this).select('span');
      span.attr('style', span.attr('style') + '; background-color: rgba(220,20,60,'+ opacity +') !important;');
    });
  };

  function _drawBars() {
    d3.select('.bar-graph').append('ul').selectAll('li')
    .data(_keysSortedByOpacity()).enter().append('li')
    .attr('class', function (id) { 
      return 'svg_'+id;
    })
    .html(_barInfo)
    .on('mouseover', _hoverBar)
    .on('click', _clickRegion);
  };

  function _keysSortedByOpacity() {
    var sortedKeys = Object.keys(Mortes);
    sortedKeys.sort(function (a, b) {
      return parseFloat(Mortes[b].opacity) - parseFloat(Mortes[a].opacity)
    });
    return sortedKeys;
  };

  function _barInfo(id) {
    var regiao = Mortes[id],
    meter = "<span class='meter' style='width: "+regiao.opacity*100+"%'>"+regiao.nome+"</span>";

    return meter;
  }

  function _hoverBar(id) {
    _classOnlyThisAs('svg_'+id, 'hover');
  };

  function _clickRegion(id) {
    _sendEventToRegion(id, 'click');
  };

  function _sendEventToRegion(id, eventName) {
    var region = document.getElementById('svg_'+id);
    d3.select(region).on(eventName).call(region);
  };

  function _draw_timeline(cod) {
    var regiao = Mortes[cod];
    console.log(Mortes);
    console.log(cod);
    var years = [];
    $.each(regiao.anos, function(i, v) {
      years.push(v);
    });
    
    $(".timeline span").sparkline(years, {
      type: 'bar',
      height: '40',
      barWidth: 20,
      barSpacing: 1,
      chartRangeMin: 0,
      barColor: '#ffffff'});
  };

  return {
    'initialize': initialize
  };
})(jQuery);

$(document).ready(function () {
  //Map.initialize($('#map'), '../data/RioGrandedoSul_MesoMicroMunicip.svg');
  Map.initialize($('#map'), '../data/Mapa_Rio_Grande_do_Sul.svg');
});