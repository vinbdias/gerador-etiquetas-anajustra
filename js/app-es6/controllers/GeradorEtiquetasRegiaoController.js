class GeradorEtiquetasRegiaoController {

    constructor() {

        this._inputSubmit = $('#gerar-etiquetas');

        this._elementoListaRegioes = $('#lista-regioes');        

        this._containerDeMensagemDeErro = $('#container-mensagem-erro');
        this._containerDeMensagemDeErro.hide();
        
        this._regiaoService = new RegiaoService();
        this._geradorEtiquetasService = new GeradorEtiquetasService();

        this._regioes = [];

        this._buscaRegioes();
    }

    _buscaRegioes() {

        this._regiaoService
        .obterRegioes()
        .then(regioes => {

            this._elementoListaRegioes.html('');
            regioes.forEach(regiao => {

                this._regioes.push(regiao);
                this._elementoListaRegioes.append('<li id="regiao-' + regiao.id + '">' + regiao.lotacao + '</li>');
            });    

            this._inputSubmit.prop('disabled', false);
        })        
        .catch(erro => console.log(erro));
    }


    submeteFormulario(evento) {

        evento.preventDefault();        
        
        this._regioes.forEach(regiao => {

            this._geradorEtiquetasService
                .gerarEtiquetasRegiao(regiao)
                .then(resposta => {

                    let htmlAnterior = $('#regiao-' + regiao.id).html();
                    let htmlAConcatenar = ' <span style="color: green;">etiquetas geradas com sucesso ' + 
                          '<a href="' + resposta.nomeXlsx + '">Arquivo para envio</a></span>';

                    $('#regiao-' + regiao.id).html('');
                    $('#regiao-' + regiao.id).html(htmlAnterior + htmlAConcatenar);
                    
                })
                .catch(erro => {

                    let htmlAnterior = $('#regiao-' + regiao.id).html();
                    let htmlAConcatenar = '<span style="color: red;"> erro ao gerar etiquetas!</span>';

                    $('#regiao-' + regiao.id).html('');
                    $('#regiao-' + regiao.id).html(htmlAnterior + htmlAConcatenar);
                    console.log(erro);

                });            
        });
        
    }
}