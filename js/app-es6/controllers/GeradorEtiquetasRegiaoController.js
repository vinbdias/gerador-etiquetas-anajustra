class GeradorEtiquetasRegiaoController {

    constructor() {

        this._containerDeMensagemDeErro = $('#container-mensagem-erro');
        this._containerDeMensagemDeErro.hide();

        this._formulario = $('#form-gera-etiquetas-regioestrts');

        this._inputRegiaoTrt = $('#regiao');
        this._regiaoService = new RegiaoService();
        this._geradorEtiquetasService = new GeradorEtiquetasService();

        this._montaInputRegiaoTrt();
    }

    _montaInputRegiaoTrt() {

        this._regiaoService
        .obterRegioes()
        .then(regioes => 

            regioes.forEach(regiao =>

                this._inputRegiaoTrt.append($('<option>', {value: regiao.id, text: regiao.lotacao}))            
        ))
        .catch(erro => console.log(erro));
    }

    _validaFormulario() {
        
        if(this._inputRegiaoTrt.val() != '') {

            return true;
        }

        return false;
    }

    submeteFormulario(evento) {

        evento.preventDefault();

        this._containerDeMensagemDeErro.html('');
        this._containerDeMensagemDeErro.hide();

        if(!this._validaFormulario()) {

            this._containerDeMensagemDeErro.html('Favor informar a região');
            this._containerDeMensagemDeErro.show();
            return false;
        }
        
        this._geradorEtiquetasService
            .gerarEtiquetasRegiao(this._inputRegiaoTrt.val())
            .then(resposta => console.log(resposta))
            .catch(erro => console.log(erro));
    }
}