/**
 * Classe que controla os eventos da tela da página de pesquisa de por nome e CPF de associados para geração de etiquetas em pdf
 */
class GeradorEtiquetasController {

    /**
     * Método construtor
     */
    constructor() {

        //Obtém e guarda os inputs de nome e cpf em atributos da classe para referência e uso futuros
        this._inputNome = $('#busca-nome');
        this._inputCpf = $('#busca-cpf');

        //Inicializa plugin jQuery TagsManager (nuvem de tags)
        this._iniciar_jQueryTagsManagerPlugin();

        //Inicializa plugin jQuery TypeAhead
        this._iniciar_jQueryTypeAheadPlugin();

        //Inicializa plugin jQuery Mask
        this._iniciar_jQueryMaskPlugin();

        //Obtém instância do modelo ListaAssociados e guarda em atributo para uso futuro
        this._listaAssociados = new ListaAssociados();
    }

    /**
     * Método responsável por incializar e amarrar o plugin TagsManager, do jQuery, aos inputs de nome e cpf. (Nuvem de tags)
     */
    _iniciar_jQueryTagsManagerPlugin() {

        this._nomeApi = this._inputNome.tagsManager();
        this._cpfApi =  this._inputCpf.tagsManager();
    }

    /**
     * Método responsável por incializar e amarrar o plugin TypeAhead, do jQuery, aos inputs de nome e cpf. (Autocompletar)
     */
    _iniciar_jQueryTypeAheadPlugin() {

        //Obtém instância do motor Bloodhound, utilizado pelo TypeAhead para pesquisa, a partir da classe BloodhoundFactory
        this._pesquisaAssociados = BloodhoundFactory.getBloodhound();

        //Inicializa o Bloodhound
        this._pesquisaAssociados.initialize();

        //Instancia de fato o TypeAhead para os inputs de nome e cpf.
        this._instanciarTypeAheadNome();
        this._instanciarTypeAheadCpf();
    }

    /**
     * Método responsável por inicializar o plugin Mask, do jQuery
     */
    _iniciar_jQueryMaskPlugin() {

        this._inputCpf.mask('999.999.999-99');
    }

    /**
     * Método que garante a entrada de apenas letras a partir do evento "onkeydown" em um input da tela
     * @param Event event: evento que disparou a execução da função. Deve ser de um dos tipos: "onkeyup", "onkeypress", "onkeydown"
     */
    apenasLetras(evento) {
        //Permitir: spacebar, backspace, delete, tab, escape, enter and .
        if($.inArray(evento.keyCode, [32, 46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            //Checar se o keycode da tecla pressionada está dentro dos intervalos de caracteres alfabéticos
            (evento.keyCode > 64 && event.keyCode < 91) ||
            (evento.keyCode > 105 && event.keyCode < 123)) {
            //Não faz nada
            return;
        }
        //Interrompe a entrada do valor da tecla
        event.preventDefault();
    }

    /**
     * Método que garante a entrada de apenas números a partir do evento "onkeydown" em um input da tela
     * @param Event event: evento que disparou a execução deste método. Deve ser de um dos tipos: "onkeyup", "onkeypress", "onkeydown"
     */
    apenasNumeros(evento) {

        //Permitir: backspace, delete, tab, escape, enter and .
        if($.inArray(evento.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             //Permitir: Ctrl/cmd+A
            (evento.keyCode == 65 && (evento.ctrlKey === true || evento.metaKey === true)) ||
             //Permitir: Ctrl/cmd+C
            (evento.keyCode == 67 && (evento.ctrlKey === true || evento.metaKey === true)) ||
             //Permitir: Ctrl/cmd+X
            (evento.keyCode == 88 && (evento.ctrlKey === true || evento.metaKey === true)) ||
             //Permitir: home, end, left, right
            (evento.keyCode >= 35 && evento.keyCode <= 39)) {
                 //Não faz nada. Permite a entrada do valor da tecla
                 return;
        }

        //Garantir que é um número. Caso contrário, interromper a entrada do valor da tecla
        if((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {

            event.preventDefault();
        }
    }

    /**
     * Método responsável por instanciar e controlar o comportamento do plugin TypeAhead, do jQuery, para o input de nome
     */
    _instanciarTypeAheadNome() {

        this._inputNome.typeahead(null, {
            displayKey: 'nome',
            source: this._pesquisaAssociados.ttAdapter()
        }).on('typeahead:selected', function (object, associado) {

            this._nomeApi.tagsManager('pushTag', associado.nome);
            this._listaAssociados.adiciona(associado);

            this._inputNome.val('');
        }.bind(this));
    }

    /**
     * Método responsável por instanciar e controlar o comportamento do plugin TypeAhead, do jQuery, para o input de cpf
     */
    _instanciarTypeAheadCpf() {

        this._inputCpf
        .typeahead(null, {
            displayKey: 'cpf',
            source: this._pesquisaAssociados.ttAdapter()
        })
        .on('typeahead:selected', function (object, associado) {

            this._cpfApi.tagsManager('pushTag', associado.cpf);
            this._listaAssociados.adiciona(associado);

            this._inputCpf.val('');
        }.bind(this));
    }

    /**
     * Método responsável por gerenciar o comportamento de "submit" da página.
     * Redireciona para a página responsável por gerar o .pdf das etiquetas.
     * @param Event event: evento responsável por acionar a execução deste método
     */
    gerarEtiquetas(evento) {

        evento.preventDefault();
        window.open('gera-etiquetas-nome-cpf.php?ids_associados=' + this._listaAssociados.obterListaIdsAssociados().join(','));
    }
}