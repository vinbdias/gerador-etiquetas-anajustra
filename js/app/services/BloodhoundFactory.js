'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Classe factory responsável por abstrair a instanciação do Bloodhound. Motor de pesquisa a ser usado como fonte de dados do plugin TypeAhead, do jQuery
 */
var BloodhoundFactory = function () {

    //Constante que armazena a url para qual o Bloodhound vai fazer suas requisições assíncronas de pesquisa
    var remoteUrl = 'pesquisa-nome-cpf.php?query=%QUERY'; //o parâmetro %QUERY da "queryString" da url nada mais é do que o valor do input, contendo o TypeAhead, que utilizará esta instância do Bloodhound

    return function () {

        /**
         * Método construtor. Não deve ser acessado, já que a factory é uma classe abstrata. Não podendo ser instanciada
         */
        function BloodhoundFactory() {
            _classCallCheck(this, BloodhoundFactory);

            //Lança um erro caso tente-se instanciar BloodhoundFactory
            throw new Error('Não é possível criar instâncias de BloodhoundFactory.');
        }

        /**
         * Método estático responsável por retornar uma instância do Bloodhound, configurada para retornar um json de associados
         *  a servir de fonte de dados para o plugin TypeAhead, do jQuery
         */


        _createClass(BloodhoundFactory, null, [{
            key: 'getBloodhound',
            value: function getBloodhound() {

                return new Bloodhound({
                    datumTokenizer: function datumTokenizer(datum) {

                        return Bloodhound.tokenizers.whitespace(datum.value);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: remoteUrl,
                        filter: function filter(associados) {

                            // Usa a função map, do jQuery, para formatar o json
                            return $.map(associados, function (associado) {

                                return {
                                    id: associado.id,
                                    nome: associado.nome,
                                    cpf: associado.cpf
                                };
                            });
                        }
                    }
                });
            }
        }]);

        return BloodhoundFactory;
    }();
}();
//# sourceMappingURL=BloodhoundFactory.js.map