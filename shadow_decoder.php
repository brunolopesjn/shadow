<?php

// Carrega as opções do comando
$options = getopt("t:h:i:v", ["help"]);

if (isset($options['h']) || isset($options['help'])) {
    echo "Shadow Encoder\n\n";
    echo "php shadow_encoder.php -t Lorem Ipsum\n";
    echo "Opção -i: Imagem a ser utilizada no processo de decodificação\n";
    echo "Opção -t: Arquivo de texto  de destino com o conteúdo da imagem\n";
    echo "Opção -v: Exibe informações dos pixels modificados\n";
    return;
}

if (!isset($options['t']) || !isset($options['i'])) {
    echo "Deve-se passar um texto e uma imagem para o encoder.\n\n";
    echo "Ex.: php shadow_encoder.php -i nova_imagem.png -t novo_texto.txt\n";
    return;
}

// Carrega a imagem na memória
$im = imagecreatefrompng($options['i']);

// Recupera o tamanho da imagem
$width = imagesx($im);
$height = imagesy($im);

$tamanhoMensagem = 0;

// Percorra os 11 primeiros pixels da imagem para extrair o tamanho do texto
for ($j = 0; $j < 11; $j++) {
    $pixel = imagecolorat($im, 0, $j); // Suponha que os bytes do tamanho estejam nos primeiros 4 pixels na primeira linha da imagem
    $r = ($pixel >> 16) & 0x01;
    $g = ($pixel >> 8) & 0x01;
    $b = $pixel & 0x01;

    $tamanhoMensagem = ($tamanhoMensagem << 1) | $r;
    $tamanhoMensagem = ($tamanhoMensagem << 1) | $g;
    $tamanhoMensagem = ($tamanhoMensagem << 1) | $b;
}

// Como foi lido um bit a mais para computar o tamanho, o mesmo deve ser retirado
$tamanhoMensagem = $tamanhoMensagem >> 1;

// Variável que armazena o byte de texto
$byte = 0;

// Variável de contagem de bits lidos
$bitsCount = 0;

// Variável que armazena a mensagem
$mensagem = "";

for ($i = 0; $i < $width; $i++) {
    for ($j = 0; $j < $height; $j++) {

        // Verifique se a mensagem já foi totalmente decodificada
        if (strlen($mensagem) >= $tamanhoMensagem) {
            break 2; // Saia dos loops
        }

        // Recupera o pixel i,j da imagem 
        $pixel = imagecolorat($im, $i, $j);

        // Extrai os valores de vermelho, verde e azul do pixel
        $red = ($pixel >> 16) & 0x01;
        $green = ($pixel >> 8) & 0x01;
        $blue = $pixel & 0x01;

        // Armazena no byte os bits menos significativos das componentes do pixel
        $byte = ($byte << 1) | $red;
        $byte = ($byte << 1) | $green;
        $byte = ($byte << 1) | $blue;
        $bitsCount += 3;

        // Se temos mais de 8 bits lidos, extrair caractere
        if ($bitsCount >= 8) {

            // Diferença de bits que excedeu a 8
            $offset = $bitsCount - 8;

            // Retira os bits excedentes da porção menos significativa e converte para caractere
            $mensagem .= chr($byte >> $offset);

            // Retira os bits já convertidos para caractere
            $byte = $byte & (0xFF >> (8 - $offset));

            // Ajusta o contador de bits retirando oito unidades
            $bitsCount -= 8;
        }
    }
}

//Gera o arquivo com a mensagem decodificada
$arquivo  = $options['t'];
if(file_put_contents($arquivo, substr($mensagem, 4)) ==! false) {
    echo "A string foi salva com sucesso no arquivo $arquivo.\n";
} else {
    echo "Ocorreu um erro ao gerar o arquivo\n";
}
