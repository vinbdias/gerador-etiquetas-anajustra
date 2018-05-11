/**
 * Classe modelo responsável por gerenciar comportamentos referentes ao armazenamento da lista de associados para quem se gerará etiquetas
 */
class ListaAssociados {

    /**
     * Método construtor
     */
    constructor() {

        //Inicializa o atributo privado lista
        this._lista = [];
    }

    /**
     * Método responsável por adicionar um associado à lista
     */ 
    adiciona(associado) {

        this._lista.push(associado);
    }

    /**
     * Getter do atributo privado lista
     */
    get lista() {

        return this._lista;
    }

    /**
     * Setter do atributo privado lista
     */ 
    set lista(lista) {

        this._lista = [].concat(lista);
    }

    /**
     * Método responsável por retornar uma lista contendo apenas os "id's" dos associados armazenados no atributo privado lista
     */
    obterListaIdsAssociados() {

        let idsAssociados = [];

        this._lista.forEach(function(associado) {
            idsAssociados.push(associado.id);
        });

        return idsAssociados;
    }
}