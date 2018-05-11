<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="/favicon.ico" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <title>ANAJUSTRA - GERADOR DE ETIQUETAS</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.css">
    <style>
        .text-on-pannel {
          background: #fff none repeat scroll 0 0;
          height: auto;
          margin-left: 20px;
          padding: 5px 8px;
          position: absolute;
          margin-top: -47px;
          border: 1px solid #337ab7;
          border-radius: 4px;
        }
        .panel {
          margin-top: 27px !important;
          border-radius: 4px;
        }
        .panel-body {
          border-radius: 4px;
          padding-top: 30px !important;
        }     

        .form-control {

            width: 50% !important;        
        }

        .tm-input-info {

            position: fixed !important;
        }  

        .twitter-typeahead .tt-query,
        .twitter-typeahead .tt-hint {
            margin-bottom: 0;
        }
        .tt-hint {
            display: block;
            width: 100%;
            height: 38px;
            padding: 8px 12px;
            font-size: 14px;
            line-height: 1.428571429;
            color: #999;
            vertical-align: middle;
            background-color: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
                  transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
        }
        .tt-dropdown-menu {
            min-width: 450px;
            margin-top: 45px !important;
            padding: 5px 0;
            background-color: #ffffff;
            border: 1px solid #cccccc;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
                  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
            background-clip: padding-box;
            float: none !important;                      

        }
        .tt-suggestion {
            display: block;
            padding: 3px 20px;
        }
        .tt-suggestion.tt-is-under-cursor {
            color: #fff;
            background-color: #428bca;
        }
        .tt-suggestion.tt-is-under-cursor a {
            color: #fff;
        }
        .tt-suggestion p {
            margin: 0;
        }

        .panel {

            padding-bottom: 50px !important;
        }
    </style>

</head>
<body>


    <div class="container">
        <div class="col-md-12" style="margin-top: 15px;"><p>&nbsp;</p><p align="center"><img src="https://www.anajustra.org.br/imagens/logo_anjst.png"/></p>
        
        <h3 align="center" style="font-weight:bold">Gerador de etiquetas PIMACO modelo 6181 (20 etiquetas)</h3><p>&nbsp;</p></div>
          <div class="panel panel-primary col-md-12">
            <div class="panel-body">
              <h4 class="text-on-pannel text-primary"><strong class="text-uppercase">Pesquisar por nome </strong></h4>
                <form action="gera-etiquetas-nome-cpf.php" id="form-gera-etiquetas" method="post">
                    <div class="form-group">
                        <label for="nome">Pesquisar nome:</label><br/>
                        <input onkeydown="geradorEtiquetasController.apenasLetras(event)" type="text" name="nome" placeholder="Nome do associado" id="busca-nome" class="busca-nome nome-input form-control tm-input-info" value="" autocomplete="off" style="float: none; clear: both;" />
                    </div>
                </form>
            </div>
          </div>
          
           <div class="panel panel-primary col-md-12">
            <div class="panel-body">
              <h4 class="text-on-pannel text-primary"><strong class="text-uppercase">Pesquisar por CPF </strong></h4>
                <form action="gera-etiquetas-cpf.php" method="get">
                    <div class="form-group">
                        <label>Pesquisar CPF:</label><br/>
                        <input onkeydown="geradorEtiquetasController.apenasNumeros(event)" type="text" name="cpf" placeholder="CPF do associado" id="busca-cpf" class="busca-cpf cpf-input form-control tm-input-info" value="" autocomplete="off"/>
                    </div>
                    <input type="hidden" name="ids_associados" id="ids-associados" value="" />
                </form>
            </div>
            
            
            
          </div>     
          
         <!--  <div class="panel panel-primary col-md-12">
            <div class="panel-body">
              <h4 class="text-on-pannel text-primary"><strong class="text-uppercase">Gerar etiquetas por região </strong></h4>
                <form action="gera-etiquetas-regiao.php" method="get">
                    <div class="form-group">
                        <label>Pesquisar região:</label><br/>
                        <input type="text" name="regiao" placeholder="EX: TRT 01..." class="busca-regiao regiao-input form-control tm-input-info" value="" autocomplete="off"/>
                    </div>
                    <div class="form-group pull-right">
                        <button class="btn btn-primary" formtarget="_blank">GERAR ETIQUETAS</button>
                    </div>
                </form>
            </div>
          </div>  -->
          
            
      </div>
      <p align="center">
        <button onclick="geradorEtiquetasController.gerarEtiquetas(event)" id="gerar-etiquetas" class="btn btn-primary" formtarget="_blank" style="margin-top: 50px;">GERAR ETIQUETAS</button> 
      </p>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>  
    <script src="node_modules/jquery-mask-plugin/dist/jquery.mask.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.js"></script>
    <!--<script src="https://rawgit.com/twitter/typeahead.js/master/dist/bloodhound.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="js/app/services/BloodhoundFactory.js"></script>
    <script src="js/app/models/ListaAssociados.js"></script>
    <script src="js/app/controllers/GeradorEtiquetasController.js"></script>


    <script type="text/javascript">
        
        var geradorEtiquetasController = new GeradorEtiquetasController();
    </script>
</body>
</html>