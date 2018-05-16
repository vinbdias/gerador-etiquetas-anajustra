class GeradorEtiquetasService extends HttpService {

    gerarEtiquetasRegiao(regiao) {

        return new Promise((resolve, reject) => {

            this
            .post('gera-etiquetas-regiaotrts.php', {regiao: regiao.id})
            .then((resposta) => {

                console.log('Etiquetas geradas com sucesso.');
                resolve(resposta);
            })
            .catch(erro => {

                console.log(erro);
                reject('Não foi possível gerar etiquetas para a região ' + regiao.lotacao);
            });
        });
    }
}