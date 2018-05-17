<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="/favicon.ico" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <title>ANAJUSTRA - GERADOR DE ETIQUETAS</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">    
    <style>
    </style>

</head>
<body>

    <div class="container">
        <div class="col-md-12" style="margin-top: 15px;"><p>&nbsp;</p><p align="center"><img src="https://www.anajustra.org.br/imagens/logo_anjst.png"/></p>
        
        <h3 align="center" style="font-weight:bold">Gerador de análises e arquivos para impressão de etiquetas</h3><p>&nbsp;</p></div>
          <div class="panel panel-primary col-md-12">
            <div class="panel-body">
              <h4><strong class="text-uppercase">Validação ViaCEP?</strong></h4>
              <div class="form-group">
                <input type="checkbox" id="validacao-viacep" name="validacao-viacep" value="" />
              </div>                
              <h4><strong class="text-uppercase">Saídas em arquivo</strong></h4>
              <div class="form-group">
                <input type="checkbox" class="tipos-saida" id="xlsx" name="tipos-saida[]" value="xlsx" />Excel PIMACO<br />
                <input type="checkbox" class="tipos-saida" id="xlsxOks" name="tipos-saida[]" value="xlsxOks" />Excel Oks<br />
                <input type="checkbox" class="tipos-saida" id="xlsxNaoOks" name="tipos-saida[]" value="xlsxNaoOks" />Excel Não Oks<br />
              </div>              
              <h4><strong class="text-uppercase">Regiões TRT</strong></h4>
            <div class="form-group">
              <ul id="lista-regioes">
                <li id="cabecalho-lista-regioes">Carregando Regiões</li>
              </ul>              
            </div>
            <div class="form-group">
                <h4><strong class="text-uppercase"><span class="glyphicon-asterisk">&nbsp;</span></strong></h4>
                <span class="small"><i>Excel PIMACO -> Planilha Excel para geração de etiquetas no PIMACO.</i></span></ul><br />
                <span class="small"><i>Excel Oks -> Planilha com análises dos cadastros de endereço que estão oks, geram linhas no arquivo de etiquetas, mas podem ter problemas.</i></span><br />
                <span class="small"><i>Excel Não Oks -> Planilha Excel com com análise dos cadastros de endereço inválidos para geração de etiquetas.</i></span>
            </div> 
            <div class="form-group alert alert-danger" id="mensagem-erro" style="display: none;"></div>             
            <p align="center">
              <button onclick="geradorEtiquetasRegiaoController.submeteFormulario(event)" id="gerar-etiquetas" class="btn btn-primary" type="submit" style="margin-top: 50px;" disabled="true">GERAR ARQUIVOS</button> 
              <button onclick="location.reload()" id="recarrega-pagina" class="btn btn-primary" type="submit" style="margin-top: 50px; display: none">GERAR NOVAMENTE</button>                           
            </p>               
          </div>                         
      </div>   
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="js/app/polyfill/fetch.js"></script>
    <script src="js/app/services/HttpService.js"></script>
    <script src="js/app/services/RegiaoService.js"></script>
    <script src="js/app/services/GeradorEtiquetasService.js"></script>
    <script src="js/app/controllers/GeradorEtiquetasRegiaoController.js"></script>
    <script type="text/javascript">     

        var geradorEtiquetasRegiaoController = new GeradorEtiquetasRegiaoController();       
    </script>
</body>
</html>