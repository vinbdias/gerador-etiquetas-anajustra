/**
 * Método responsável por tratar requisições que tratam de gerar análises de cadastro de endereço ou arquivos para etiquetas
 */
class GeradorEtiquetasService extends HttpService {

    /**
     * Método responsável fazer uma requisição à página gera-etiquetas-regiaotrts.php
     * @param JSON dados
     */
    gerarEtiquetasRegiao(dados) {

        return new Promise((resolve, reject) => {

            this
            .post('gera-etiquetas-regiaotrts.php', dados)
            .then((resposta) => {

                console.log('Etiquetas geradas com sucesso.');
                resolve(resposta);
            })
            .catch(erro => {

                console.log(erro);
                reject('Não foi possível gerar etiquetas para a região ' + dados.regiao.lotacao);
            });
        });
    }
}