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
        
        <h3 align="center" style="font-weight:bold">Gerador de etiquetas PIMACO modelo 6181 (20 etiquetas)</h3><p>&nbsp;</p></div>
          <div class="panel panel-primary col-md-12">
            <div class="panel-body">
              <h4><strong class="text-uppercase">Regiões TRT</strong></h4>
              <ul id="lista-regioes">
                  <li id="cabecalho-lista-regioes">Carregando Regiões</li>
              </ul>
            </div>
          </div>                         
      </div>
      <p align="center">
        <button onclick="geradorEtiquetasRegiaoController.submeteFormulario(event)" id="gerar-etiquetas" class="btn btn-primary" type="submit" style="margin-top: 50px;" disabled="true">GERAR ANÁLISES REGIÕES</button> 
      </p>
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