/**
 * Classe que controla os eventos da tela da página de geração de análises de cadastro de endereço de associados e geração de arquivos para etiqueta
 */
class GeradorEtiquetasRegiaoController {

    /**
     * Método construtor
     */
    constructor() {

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
    _buscaRegioes() {

        this._regiaoService
            .obterRegioes()
            .then(regioes => {

                this._elementoListaRegioes.html('');
                regioes.forEach(regiao => {

                    this._regioes.push(regiao);
                    this._elementoListaRegioes.append('<li id="regiao-' + regiao.id + '">' + regiao.lotacao + '</li>');
                });    

                this._inputSubmitButton.prop('disabled', false);
            })        
            .catch(erro => { throw new Error(erro) });
    }

    _obtemValoresTiposSaidaCheckboxGroup() {

        this._inputTiposSaidaGroup.forEach(elemento => {
            
            this._tiposSaida.push({
                id: elemento.attr('id'),
                valor: (elemento.prop('checked') ? 1 : 0)
            });
        });    
    }


    /**
     * Método responsável por gerenciar a submissão da página
     * @param Event evento: evento responsável por acionar a execução deste método
     */
    submeteFormulario(evento) {

        evento.preventDefault();        

        this._preparaSubmissao();  

        if(!this._validaSubmissao()) {

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
    _preparaSubmissao(regiao) {    

        this._obtemValoresTiposSaidaCheckboxGroup();

        return {
                regiao: regiao,
                tiposSaida: this._tiposSaida,
                validaViaCEP: (this._inputValidaViaCEP.prop('checked') ? 1 : 0)
            };
    }

    /**
     * Método que posta as submissões de cada região
     */
    _postaSubmissao() {

        this._inputSubmitButton.prop('disabled', true);
        this._inputSubmitButton.hide();

        let dadosSubmissao = this._preparaSubmissao();

        this._regioes.forEach(regiao => {

            let htmlAnterior = $('#regiao-' + regiao.id).html();
            let htmlAConcatenar = '';
            $('#regiao-' + regiao.id).html(htmlAnterior + ' processando...');

            this._geradorEtiquetasService
                .gerarEtiquetasRegiao(this._preparaSubmissao(regiao))
                .then(resposta => this._trataResposta(regiao.id, resposta))
                .catch(erro => {
                    
                    htmlAConcatenar = '<span style="color: red;"> erro ao gerar etiquetas!</span>';

                    $('#regiao-' + regiao.id).html('');
                    $('#regiao-' + regiao.id).html(htmlAnterior + htmlAConcatenar);                    
                });            
        });         
    }

    /**
     * Método que trata a resposta
     * @param int regiaoId
     * @param JSON resposta
     */
    _trataResposta(regiaoId, resposta) {
        
        if(resposta.excecoes.length) {

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
    _imprimeExcecoes(regiaoId, excecoes) {

        let htmlAnterior = $('#regiao-' + regiaoId).html();
        htmlAnterior = htmlAnterior.replace('processando...', '<span style="color: orange;">completo.</span> ');
        let htmlAConcatenar = '';

        excecoes.forEach(excecao => {

            htmlAConcatenar += ' <span style="color: orange;"><strong>' + excecao + '</strong></span>';
        });

        $('#regiao-' + regiaoId).html(htmlAnterior + htmlAConcatenar); 
    }

    /**
     * Método que imprime a resposta
     * @param int regiaoId
     * @param JSON resposta
     */
    _imprimeResposta(regiaoId, resposta) {

        let htmlAnterior = $('#regiao-' + regiaoId).html();
        htmlAnterior = htmlAnterior.replace('processando...', '<span style="color: green;"><strong>completo!</strong></span> ');

        let htmlAConcatenar = '';        

        htmlAConcatenar = '<span style="color: green;">' + resposta.mensagem + ' ' + 
               (resposta.nomeXlsx != '' ? '<a href="' + resposta.nomeXlsx + '" style="color: blue; text-decoration: underline;">Excel PIMACO</a> ' : '') +
               (resposta.nomeXlsxOk != '' ? '<a href="' + resposta.nomeXlsxOk + '" style="color: green; text-decoration: underline;">Excel OKs</a> ' : '') +
               (resposta.nomeXlsxNaoOk != '' ? '<a href="' + resposta.nomeXlsxNaoOk + '" style="color: orange; text-decoration: underline;">Excel não OKs</a>'  : '') +
              '</span> ';
        
        $('#regiao-' + regiaoId).html(htmlAnterior + htmlAConcatenar);         
    }

    /**
     * Método que valida a submissao
     * @return boolean
     */
    _validaSubmissao() {

        let retorno = false;

        this._tiposSaida.forEach(tipoSaida => {

            if(tipoSaida.valor > 0)
                retorno = true;
                
        });

        return retorno;
    }
}