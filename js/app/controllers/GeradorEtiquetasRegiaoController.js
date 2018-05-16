'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var GeradorEtiquetasRegiaoController = function () {
    function GeradorEtiquetasRegiaoController() {
        _classCallCheck(this, GeradorEtiquetasRegiaoController);

        this._inputSubmit = $('#gerar-etiquetas');

        this._elementoListaRegioes = $('#lista-regioes');

        this._containerDeMensagemDeErro = $('#container-mensagem-erro');
        this._containerDeMensagemDeErro.hide();

        this._regiaoService = new RegiaoService();
        this._geradorEtiquetasService = new GeradorEtiquetasService();

        this._regioes = [];

        this._buscaRegioes();
    }

    _createClass(GeradorEtiquetasRegiaoController, [{
        key: '_buscaRegioes',
        value: function _buscaRegioes() {
            var _this = this;

            this._regiaoService.obterRegioes().then(function (regioes) {

                _this._elementoListaRegioes.html('');
                regioes.forEach(function (regiao) {

                    _this._regioes.push(regiao);
                    _this._elementoListaRegioes.append('<li id="regiao-' + regiao.id + '">' + regiao.lotacao + '</li>');
                });

                _this._inputSubmit.prop('disabled', false);
            }).catch(function (erro) {
                return console.log(erro);
            });
        }
    }, {
        key: 'submeteFormulario',
        value: function submeteFormulario(evento) {
            var _this2 = this;

            evento.preventDefault();

            this._regioes.forEach(function (regiao) {

                _this2._geradorEtiquetasService.gerarEtiquetasRegiao(regiao).then(function (resposta) {

                    var htmlAnterior = $('#regiao-' + regiao.id).html();
                    var htmlAConcatenar = ' <span style="color: green;">etiquetas geradas com sucesso ' + '<a href="' + resposta.nomeXlsx + '">Arquivo para envio</a></span>';

                    $('#regiao-' + regiao.id).html('');
                    $('#regiao-' + regiao.id).html(htmlAnterior + htmlAConcatenar);
                }).catch(function (erro) {

                    var htmlAnterior = $('#regiao-' + regiao.id).html();
                    var htmlAConcatenar = '<span style="color: red;"> erro ao gerar etiquetas!</span>';

                    $('#regiao-' + regiao.id).html('');
                    $('#regiao-' + regiao.id).html(htmlAnterior + htmlAConcatenar);
                    console.log(erro);
                });
            });
        }
    }]);

    return GeradorEtiquetasRegiaoController;
}();
//# sourceMappingURL=GeradorEtiquetasRegiaoController.js.map