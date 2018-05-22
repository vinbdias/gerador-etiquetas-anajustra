'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Classe que controla os eventos da tela da página de geração de análises de cadastro de endereço de associados e geração de arquivos para etiqueta
 */
var GeradorEtiquetasRegiaoController = function () {

        /**
         * Método construtor
         */
        function GeradorEtiquetasRegiaoController() {
                _classCallCheck(this, GeradorEtiquetasRegiaoController);

                this._inputSubmitButton = $('#gerar-etiquetas');

                this._inputSubmitRecarregaPagina = $('#recarrega-pagina');

                this._inputTiposSaidaGroup = [$('#xlsx'), $('#xlsxOks'), $('#xlsxNaoOks')];
                this._tiposSaida = [];

                this._inputValidaViaCEP = $('#validacao-viacep');

                this._divMensagemErro = $('#mensagem-erro');

                this._elementoListaRegioes = $('#lista-regioes');

                this._containerDeMensagemDeErro = $('#container-mensagem-erro');
                this._containerDeMensagemDeErro.hide();

                this._regiaoService = new RegiaoService();
                this._geradorEtiquetasService = new GeradorEtiquetasService();

                this._regioes = [];

                this._buscaRegioes();
        }

        /**
         * Método que busca e armazena JSON contendo as regiões
         */


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

                                _this._inputSubmitButton.prop('disabled', false);
                        }).catch(function (erro) {
                                throw new Error(erro);
                        });
                }
        }, {
                key: '_obtemValoresTiposSaidaCheckboxGroup',
                value: function _obtemValoresTiposSaidaCheckboxGroup() {
                        var _this2 = this;

                        this._inputTiposSaidaGroup.forEach(function (elemento) {

                                _this2._tiposSaida.push({
                                        id: elemento.attr('id'),
                                        valor: elemento.prop('checked') ? 1 : 0
                                });
                        });
                }

                /**
                 * Método responsável por gerenciar a submissão da página
                 * @param Event evento: evento responsável por acionar a execução deste método
                 */

        }, {
                key: 'submeteFormulario',
                value: function submeteFormulario(evento) {

                        evento.preventDefault();

                        this._preparaSubmissao();

                        if (!this._validaSubmissao()) {

                                this._divMensagemErro.html('Favor informar um tipo de saída em arquivo');
                                this._divMensagemErro.show();

                                return false;
                        }

                        this._divMensagemErro.html('');
                        this._divMensagemErro.hide();

                        this._postaSubmissao();
                }

                /**
                 * Método que prepara os dados para submissao
                 * @param JSON regiao
                 * @return JSON
                 */

        }, {
                key: '_preparaSubmissao',
                value: function _preparaSubmissao(regiao) {

                        this._obtemValoresTiposSaidaCheckboxGroup();

                        return {
                                regiao: regiao,
                                tiposSaida: this._tiposSaida,
                                validaViaCEP: this._inputValidaViaCEP.prop('checked') ? 1 : 0
                        };
                }

                /**
                 * Método que posta as submissões de cada região
                 */

        }, {
                key: '_postaSubmissao',
                value: function _postaSubmissao() {
                        var _this3 = this;

                        this._inputSubmitButton.prop('disabled', true);
                        this._inputSubmitButton.hide();

                        var dadosSubmissao = this._preparaSubmissao();

                        this._regioes.forEach(function (regiao) {

                                var htmlAnterior = $('#regiao-' + regiao.id).html();
                                var htmlAConcatenar = '';
                                $('#regiao-' + regiao.id).html(htmlAnterior + ' processando...');

                                _this3._geradorEtiquetasService.gerarEtiquetasRegiao(_this3._preparaSubmissao(regiao)).then(function (resposta) {
                                        return _this3._trataResposta(regiao.id, resposta);
                                }).catch(function (erro) {

                                        htmlAConcatenar = '<span style="color: red;"> erro ao gerar etiquetas!</span>';

                                        $('#regiao-' + regiao.id).html('');
                                        $('#regiao-' + regiao.id).html(htmlAnterior + htmlAConcatenar);
                                        throw new Error(erro);
                                });
                        });
                }

                /**
                 * Método que trata a resposta
                 * @param int regiaoId
                 * @param JSON resposta
                 */

        }, {
                key: '_trataResposta',
                value: function _trataResposta(regiaoId, resposta) {

                        if (resposta.excecoes.length) {

                                this._imprimeExcecoes(regiaoId, resposta.excecoes);

                                return false;
                        }

                        this._imprimeResposta(regiaoId, resposta);
                }

                /**
                 * Método que imprime as exceções
                 * @param int regiaoId
                 * @param JSON resposta
                 */

        }, {
                key: '_imprimeExcecoes',
                value: function _imprimeExcecoes(regiaoId, excecoes) {

                        var htmlAnterior = $('#regiao-' + regiaoId).html();
                        htmlAnterior = htmlAnterior.replace('processando...', '<span style="color: orange;">completo.</span> ');
                        var htmlAConcatenar = '';

                        excecoes.forEach(function (excecao) {

                                htmlAConcatenar += ' <span style="color: orange;"><strong>' + excecao + '</strong></span>';
                        });

                        $('#regiao-' + regiaoId).html(htmlAnterior + htmlAConcatenar);
                }

                /**
                 * Método que imprime a resposta
                 * @param int regiaoId
                 * @param JSON resposta
                 */

        }, {
                key: '_imprimeResposta',
                value: function _imprimeResposta(regiaoId, resposta) {

                        var htmlAnterior = $('#regiao-' + regiaoId).html();
                        htmlAnterior = htmlAnterior.replace('processando...', '<span style="color: green;"><strong>completo!</strong></span> ');

                        var htmlAConcatenar = '';

                        htmlAConcatenar = '<span style="color: green;">' + resposta.mensagem + ' ' + (resposta.nomeXlsx != '' ? '<a href="' + resposta.nomeXlsx + '" style="color: blue; text-decoration: underline;">Excel PIMACO</a> ' : '') + (resposta.nomeXlsxOk != '' ? '<a href="' + resposta.nomeXlsxOk + '" style="color: green; text-decoration: underline;">Excel OKs</a> ' : '') + (resposta.nomeXlsxNaoOk != '' ? '<a href="' + resposta.nomeXlsxNaoOk + '" style="color: orange; text-decoration: underline;">Excel não OKs</a>' : '') + '</span> ';

                        $('#regiao-' + regiaoId).html(htmlAnterior + htmlAConcatenar);
                }

                /**
                 * Método que valida a submissao
                 * @return boolean
                 */

        }, {
                key: '_validaSubmissao',
                value: function _validaSubmissao() {

                        var retorno = false;

                        this._tiposSaida.forEach(function (tipoSaida) {

                                if (tipoSaida.valor > 0) retorno = true;
                        });

                        return retorno;
                }
        }]);

        return GeradorEtiquetasRegiaoController;
}();
//# sourceMappingURL=GeradorEtiquetasRegiaoController.js.map