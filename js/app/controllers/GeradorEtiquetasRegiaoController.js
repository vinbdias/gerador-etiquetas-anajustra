'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var GeradorEtiquetasRegiaoController = function () {
        function GeradorEtiquetasRegiaoController() {
                _classCallCheck(this, GeradorEtiquetasRegiaoController);

                this._containerDeMensagemDeErro = $('#container-mensagem-erro');
                this._containerDeMensagemDeErro.hide();

                this._formulario = $('#form-gera-etiquetas-regioestrts');

                this._inputRegiaoTrt = $('#regiao');
                this._regiaoService = new RegiaoService();
                this._geradorEtiquetasService = new GeradorEtiquetasService();

                this._montaInputRegiaoTrt();
        }

        _createClass(GeradorEtiquetasRegiaoController, [{
                key: '_montaInputRegiaoTrt',
                value: function _montaInputRegiaoTrt() {
                        var _this = this;

                        this._regiaoService.obterRegioes().then(function (regioes) {
                                return regioes.forEach(function (regiao) {
                                        return _this._inputRegiaoTrt.append($('<option>', { value: regiao.id, text: regiao.lotacao }));
                                });
                        }).catch(function (erro) {
                                return console.log(erro);
                        });
                }
        }, {
                key: '_validaFormulario',
                value: function _validaFormulario() {

                        if (this._inputRegiaoTrt.val() != '') {

                                return true;
                        }

                        return false;
                }
        }, {
                key: 'submeteFormulario',
                value: function submeteFormulario(evento) {

                        evento.preventDefault();

                        this._containerDeMensagemDeErro.html('');
                        this._containerDeMensagemDeErro.hide();

                        if (!this._validaFormulario()) {

                                this._containerDeMensagemDeErro.html('Favor informar a região');
                                this._containerDeMensagemDeErro.show();
                                return false;
                        }

                        this._geradorEtiquetasService.gerarEtiquetasRegiao(this._inputRegiaoTrt.val()).then(function (resposta) {
                                return console.log(resposta);
                        }).catch(function (erro) {
                                return console.log(erro);
                        });
                }
        }]);

        return GeradorEtiquetasRegiaoController;
}();
//# sourceMappingURL=GeradorEtiquetasRegiaoController.js.map