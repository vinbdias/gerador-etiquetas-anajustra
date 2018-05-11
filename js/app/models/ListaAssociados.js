"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/**
 * Classe modelo responsável por gerenciar comportamentos referentes ao armazenamento da lista de associados para quem se gerará etiquetas
 */
var ListaAssociados = function () {

    /**
     * Método construtor
     */
    function ListaAssociados() {
        _classCallCheck(this, ListaAssociados);

        //Inicializa o atributo privado lista
        this._lista = [];
    }

    /**
     * Método responsável por adicionar um associado à lista
     */


    _createClass(ListaAssociados, [{
        key: "adiciona",
        value: function adiciona(associado) {

            this._lista.push(associado);
        }

        /**
         * Getter do atributo privado lista
         */

    }, {
        key: "obterListaIdsAssociados",


        /**
         * Método responsável por retornar uma lista contendo apenas os "id's" dos associados armazenados no atributo privado lista
         */
        value: function obterListaIdsAssociados() {

            var idsAssociados = [];

            this._lista.forEach(function (associado) {
                idsAssociados.push(associado.id);
            });

            return idsAssociados;
        }
    }, {
        key: "lista",
        get: function get() {

            return this._lista;
        }

        /**
         * Setter do atributo privado lista
         */
        ,
        set: function set(lista) {

            this._lista = [].concat(lista);
        }
    }]);

    return ListaAssociados;
}();
//# sourceMappingURL=ListaAssociados.js.map