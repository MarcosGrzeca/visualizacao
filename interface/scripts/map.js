Map = (function($) {
    var Mortes = {};

    function initialize(element, svgPath) {
        $("#visualizacoes-gerais").addClass("hide");
        d3.xml(svgPath, 'image/svg+xml', function(xml) {
            element.html(xml.documentElement);

            _setupCallbacks();
            _loadEstupros(function() {
                var focusedElementSlug = window.location.hash.replace('#', '');
                // Use Soledade as default
                if (focusedElementSlug == '') { focusedElementSlug = 'esmeralda'; };
                _focusInto(focusedElementSlug);
                _drawBars();
                _colorRegions();
                $("#visualizacoes-gerais").removeClass("hide");
            });
        });
    };

    function _setupCallbacks() {
        d3.selectAll('#map path')
            .on('mouseover', _hoverRegion)
            .on('click', _selectRegion);
    };

    function _hoverRegion() {
        _classOnlyThisAs(this.id, 'hover');
    };

    function _classOnlyThisAs(id, className) {
        d3.selectAll('#map .' + className).classed(className, false);
        d3.selectAll('#map .' + id).classed(className, true);
        idMunicipioSelecionado = id;
    };

    function getIdSelecionado() {
        var id = this.id;
        return id.replace(/.*_/, '');
    }

    function _selectRegion() {
        try {
            var id = this.id,
                codigo = id.replace(/.*_/, '');
            _classOnlyThisAs(id, 'cidade_ativa');
            _draw_timeline(codigo);
            _showInfo(codigo);
            //_montarScatterPlot(codigo);
            window.location.hash = $.slug(Mortes[codigo].nome);
        } catch (e) {

        }
    };

    function _showInfo(codigo) {
        var cidade = Mortes[codigo];
        if (!cidade) {
            return; }
        ranking = _keysSortedByOpacity().indexOf(codigo) + 1,
            $('.info-mun h3').text(cidade.nome);
        $('.population em').text(cidade.populacao);
        $('.ranking em').text(ranking + 'ª');
        $('.proporcao em').text(parseInt(cidade.proporcao));
        $('.mortesTotal em').text(parseInt(cidade.ocorrencias));
        $(".numTotalCidades").html("/" + Object.keys(Mortes).length);
    };

    function _formatNumber(number) {
        return number.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    };

    function _loadEstupros(callback) {
        wait();
        var filtros = 'cid=' + $('input[name=cid]:checked').val() + "&sexo=" + $('input[name=sexo]:checked').val() + "&conteudo=" + $('input[name=conteudo]:checked').val();
        var url = 'http://localhost/visualizacao/servidor/dados.php?';
        if ($("#compararAnos").attr("checked")) {
            url = 'http://localhost/visualizacao/servidor/dados.comparativo.php?anoBase1=' + $("#anoBase1").val() + "&anoBase2=" + $("#anoBase2").val() + "&";
        }
        $.getJSON(url + filtros, function(data) {
            Mortes = data;
            callback();
            closeWait();
        });
    };

    function _focusInto(slug) {
        var element;
        for (id in Mortes) {
            if ($.slug(Mortes[id].nome) == slug) {
                element = document.getElementById('svg_' + id);
                break;
            }
        }

        if (!element) {
            return; }

        d3.select(element).on('click').call(element);
    };

    function _ehComparativo() {
        return $("#compararAnos").attr("checked");
       
    }

    function _colorRegions() {
        $.each(Mortes, function(key, value) {
            if (parseFloat(value["opacity"]) == 0 && _ehComparativo()) {
                $("#svg_" + key).css("fill", "rgba(0,0,255, 0.1)");
            } else if (parseFloat(value["opacity"]) > 0 || !_ehComparativo()) {
                $("#svg_" + key).css("fill", "rgba(153,0,0," + value["opacity"] + ")");
            } else {
                $("#svg_" + key).css("fill", "rgba(0,165,0," + parseFloat(value["opacity"]) * -1 + ")");
            }
        });

        d3.selectAll('#map .bar-graph li')
            .each(function(id) {
                var d3RegionMap = d3.select('path.' + this.classList[0]),
                    opacity = d3RegionMap.attr('style').replace(/.*: (.*);?/, '$1'),
                    span = d3.select(this).select('span');
                span.attr('style', span.attr('style') + '; background-color: rgba(220,20,60,' + opacity + ') !important;');
            });
    };

    function _drawBars() {
        try {
            $(".bar-graph > ul").remove();
            d3.select('.bar-graph').append('ul').selectAll('li')
                .data(getTopMunicipios()).enter().append('li')
                .attr('class', function(id) {
                    return 'svg_' + id;
                })
                .html(_barInfo)
                .on('mouseover', _hoverBar)
                .on('click', _clickRegion);
        } catch (e) {
            console.log(e);
        }
    };

    function getTopMunicipios() {
        var valores = _keysSortedByOpacity();
        var tops = [];
        var ind = 0;
        $.each(valores, function(key, value) {
            /*if (ind <= 9) {
              tops.push(value);
            } else if (ind > Object.keys(Mortes).length - 9) {
              tops.push(value);
            }*/
            ind++;
            if ($("#svg_" + value).length != 0) {
                tops.push(value);
            }
        });
        return tops;
    }

    function _keysSortedByOpacity() {
        var sortedKeys = Object.keys(Mortes);
        sortedKeys.sort(function(a, b) {
            return parseFloat(Mortes[b].opacity) - parseFloat(Mortes[a].opacity)
        });
        return sortedKeys;
    };

    function _barInfo(id) {
        var regiao = Mortes[id];
        if (parseFloat(regiao.opacity) == 0 && _ehComparativo()) {
            meter = "<span class='meter' style='width: 10%; background-color: rgba(0,0,255, 1)'>" + regiao.nome + "</span>";
        } else if (parseFloat(regiao.opacity) > 0 || !_ehComparativo()) {
            meter = "<span class='meter' style='width: " + regiao.opacity * 100 + "%; background-color: rgba(220, 20, 60, " + regiao.opacity + ")'>" + regiao.nome + "</span>";
        } else {
            meter = "<span class='meter' style='width: " + (parseFloat(regiao.opacity) * -100) + "%; background-color: rgba(20, 198, 60, " + parseFloat(regiao.opacity) * -1 + ")'>" + regiao.nome + "</span>";
        }
        return meter;
    }

    function _hoverBar(id) {
        _classOnlyThisAs('svg_' + id, 'hover');
    };

    function _clickRegion(id) {
        _sendEventToRegion(id, 'click');
    };

    function _sendEventToRegion(id, eventName) {
        var region = document.getElementById('svg_' + id);
        d3.select(region).on(eventName).call(region);
    };

    function _draw_timeline(cod) {
        var regiao = Mortes[cod];
        if (regiao) {
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
                barColor: '#ffffff'
            });
        } else {
            console.log(Mortes);
            console.log(cod);
        }
    };

    return {
        'initialize': initialize,
        "montarScatterPlot": montarScatterPlot,
        "getIdSelecionado": getIdSelecionado
    };
})(jQuery);

$(document).ready(function() {
    Map.initialize($('#map'), 'interface/mapa/Mapa_Rio_Grande_do_Sul.svg');
});