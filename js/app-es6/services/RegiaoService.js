class RegiaoService extends HttpService {

    obterRegioes() {

        return new Promise((resolve, reject) => {

            this
            .get('pesquisa-regioestrts.php')
            .then(regioes => resolve(regioes))
            .catch(() => reject('Não foi possível obter as regiões.'));             
        });     
    }
}