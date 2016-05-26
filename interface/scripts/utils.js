var idMunicipioSelecionado = 0;

$.slug = (function() {
    var in_chrs = 'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ _',
        out_chrs = 'aaaaaceeeeiiiinooooouuuuyyaaaaaceeeeiiiinooooouuuuy--',
        chars_rgx = new RegExp('[' + in_chrs + ']', 'gi'),
        transl = {},
        i,
        lookup = function(m) {
            return transl[m] || m;
        };

    for (i = 0; i < in_chrs.length; i++) {
        transl[in_chrs[i]] = out_chrs[i];
    }

    return function(s) {
        return s.replace(chars_rgx, lookup).toLowerCase();
    }
})();


function exibirGraficosPorMunicipio() {
    wait();
    $(".porMunicipio").removeClass("hide");
    $(".visaoGeral").addClass("hide");
    montarScatterPlot();
    d3.select(self.frameElement).style("height", "700px");
}

function exibirMapa() {
    $(".porMunicipio").addClass("hide");
    $(".visaoGeral").removeClass("hide");
}
function wait() {
    $(".loader").removeClass("hide");
}

function closeWait() {
    $(".loader").addClass("hide");
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function ocultarVisualizacoesMunicipio() {
    $(".abas").addClass("hide").filter(".active").removeClass("active");
}

function trocarAba(aba, divExibir) {
    wait();
    $(".porMunicipio .nav-tabs > li.active").removeClass("active");
    aba.parent().addClass("active").removeClass("hide");
    $("#" + divExibir).removeClass("hide").show();
}

function atualizarGrafico() {
    Map.initialize($('#map'), 'interface/mapa/Mapa_Rio_Grande_do_Sul.svg');
}

function minimizarFiltros() {
    $("#box-filtros").addClass("hide");
    $("#btn_min").addClass("hide");
    $("#btn_max").removeClass("hide");
}

function maximizarFiltros() {
    $("#box-filtros").removeClass("hide");
    $("#btn_min").addClass("hide");
    $("#btn_max").removeClass("hide");
}

/*Scatter */
function montarScatterPlot() {
    $.ajax({
            url: "http://localhost/visualizacao/servidor/scatter.php?codMun=" + getIdMunicipioSelecionado()
        })
        .done(function(data) {
            renderScatterPlot();
        });
};

function renderScatterPlot() {
    $("#scatterPlot").html("");
    var sm = new ScatterMatrix('servidor/dados_scatter.csv', undefined, "scatterPlot");
    sm.render()
    closeWait();
}

function getIdMunicipioSelecionado() {
    return idMunicipioSelecionado.replace(/.*_/, '');
}

function montarSequenceSunburst() {
    wait();
    Sequence.initialize();
}

/*setTimeout(function() {
    exibirGraficosPorMunicipio();
}, 3000);*/