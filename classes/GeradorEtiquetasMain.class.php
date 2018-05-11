<?php 

    require_once(__ROOT__ . 'fpdf' . DS . 'fpdf.php');
    use \ForceUTF8\Encoding;

    class GeradorEtiquetasMain {

        private $mesq = 7;
        private $mdir = 8;
        private $msup = 22;
        private $leti = 22;
        private $aeti = 25.4;
        private $ehet = 100;
        private $fpdf = NULL;

        private $type = '';

        public function __construct($type = '') {

            if($type == 'fpdf') {

                $this->_iniciaFPDF();
            }

            $this->type = $type;
        }

        private function _iniciaFPDF() {

            $this->fpdf = new FPDF('P','mm','Letter'); // Cria um arquivo novo tipo carta, na vertical.

            //$this->fpdf->Open(); // inicia documento
            $this->fpdf->AddPage(); // adiciona a primeira pagina
            $this->fpdf->SetMargins('18','1,7'); // Define as margens do documento
            $this->fpdf->SetLeftMargin('1'); // Define as margens esquerda do documento
            $this->fpdf->SetAuthor("ANAJUSTRA"); // Define o autor
            $this->fpdf->SetFont('helvetica','B',8.5); // Define a fonte
            $this->fpdf->SetDisplayMode();

            $this->linha = 0;
            $this->coluna = 0;
        }

        public function montaEtiquetaFPDF($nome, $cep, $endereco, $bairro_cidade_estado = NULL) {

            if($this->linha == 10) {

                $this->fpdf->AddPage();
                $this->linha = 0;
            }
            
            if($this->coluna == 2) { // Se for a terceira coluna

                $this->coluna = 0; // $coluna volta para o valor inicial
                $this->linha = $linha +1; // $linha é igual ela mesma +1
            }
            
            if($this->linha == 10) { // Se for a última linha da página

                $this->fpdf->AddPage(); // Adiciona uma nova página
                $linha = 0; // $linha volta ao seu valor inicial
            }
            
            $posicaoV = $this->linha*$aeti;
            $posicaoH = $this->coluna*$leti;
            
            if($this->coluna == "0") { // Se a coluna for 0

                $somaH = $this->mesq; // Soma Horizontal é apenas a margem da esquerda inicial
            } 
            else { // Senão

                $somaH = $this->mesq+$posicaoH; // Soma Horizontal é a margem inicial mais a posiçãoH
            }
            
            if($this->linha == 0 ) { // Se a linha for 0

                $somaV = $this->msup; // Soma Vertical é apenas a margem superior inicial
            } 
            else { // Senão

                $somaV = $this->msup+$posicaoV; // Soma Vertical é a margem superior inicial mais a posiçãoV
            }
            
            $this->fpdf->Text($somaH,$somaV,Encoding::toWin1252($nome)); // Imprime o nome da pessoa de acordo com as coordenadas

            $this->fpdf->Text($somaH,$somaV+4,Encoding::toWin1252($ende)); // Imprime o endereço da pessoa de acordo com as coordenadas

            if(isset($bairro_cidade_estado) && $bairro_cidade_estado != '') {

                $this->fpdf->Text($somaH,$somaV+8,Encoding::toWin1252($bairro_cidade_estado)); // Imprime o cep da pessoa de acordo com as coordenadas
            }

            $this->fpdf->Text($somaH,$somaV+12, 'CEP: ' . $cep);        
            
            $this->coluna = $this->coluna+1;
        }

        public function saida() {

            if($this->type == 'fpdf') {

                $this->fpdf->Output();
            }
        }
    }