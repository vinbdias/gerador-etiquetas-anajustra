/**
 * Classe que implementa chamadas a métodos fetch
 */
class HttpService {

    /**
     * Método responsável por tratar erros de uma resposta de uma requisição, caso haja um
     * @param fetch response res
     */
    _handleErrors(res) {

        if(!res.ok) throw new Error(res.statusText);

        return res;
    }

    /**
     * Método responsável por fazer requisições fetch do tipo get
     * @param String url
     */
    get(url) {

        return fetch(url)
            .then(res => this._handleErrors(res))
            .then(res => res.json());
    }

    /**
     * Método responsável por fazer requisições fetch do tipo post
     * @param String url
     * @param JSON dado
     */
    post(url, dado) {        

        return fetch(url, {

            headers: { 'Content-type': 'application/json' },
            method: 'post',
            mode: 'same-origin',
            credentials: 'same-origin',            
            body: JSON.stringify(dado)
        })
        .then(res => this._handleErrors(res))
        .then(res => res.json());
    }
}
