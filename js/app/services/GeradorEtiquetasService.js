'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

/**
 * Método responsável por tratar requisições que tratam de gerar análises de cadastro de endereço ou arquivos para etiquetas
 */
var GeradorEtiquetasService = function (_HttpService) {
    _inherits(GeradorEtiquetasService, _HttpService);

    function GeradorEtiquetasService() {
        _classCallCheck(this, GeradorEtiquetasService);

        return _possibleConstructorReturn(this, (GeradorEtiquetasService.__proto__ || Object.getPrototypeOf(GeradorEtiquetasService)).apply(this, arguments));
    }

    _createClass(GeradorEtiquetasService, [{
        key: 'gerarEtiquetasRegiao',


        /**
         * Método responsável fazer uma requisição à página gera-etiquetas-regiaotrts.php
         * @param JSON dados
         */
        value: function gerarEtiquetasRegiao(dados) {
            var _this2 = this;

            return new Promise(function (resolve, reject) {

                _this2.post('gera-etiquetas-regiaotrts.php', dados).then(function (resposta) {

                    console.log('Etiquetas geradas com sucesso.');
                    resolve(resposta);
                }).catch(function (erro) {

                    console.log(erro);
                    reject('Não foi possível gerar etiquetas para a região ' + dados.regiao.lotacao);
                });
            });
        }
    }]);

    return GeradorEtiquetasService;
}(HttpService);
//# sourceMappingURL=GeradorEtiquetasService.js.map