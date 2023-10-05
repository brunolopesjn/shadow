<?php

// Carrega as opções do comando
$options = getopt("t:h:i:o:v", ["help"]);

if (isset($options['h']) || isset($options['help'])) {
    echo "Shadow Encoder\n\n";
    echo "php shadow_encoder.php -t Lorem Ipsum\n";
    echo "Opção -i: Imagem a ser utilizada no processo de codificação\n";
    echo "Opção -t: Arquivo de texto com o conteúdo a ser incorporado na imagem\n";
    echo "Opção -o: Nome da nova imagem\n";
    echo "Opção -v: Exibe informações dos pixels modificados\n";
    return;
}

if (!isset($options['t']) || !isset($options['i']) || !isset($options['o'])) {
    echo "Deve-se passar um texto e uma imagem para o encoder.\n\n";
    echo "Ex.: php shadow_encoder.php -i imagem.png -t texto.txt -o nova_imagem\n";
    return;
}

// Carrega o texto do arquivo especificado
$mensagem = file_get_contents($options['t']);

// Gera um novo texto adicionando o tamanho do mesmo no cabeçalho
$tamanhoMensagem = strlen($mensagem);
$tamanhoCodificado = pack('N', $tamanhoMensagem);
$mensagemCodificada = $tamanhoCodificado . $mensagem;

// Carrega a imagem na memória
$im = imagecreatefrompng($options['i']);

// Converte a mensagem em uma sequência de bits
$bits = '';
foreach (str_split($mensagemCodificada) as $char) {
    $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
}

// Recupera o tamanho da imagem
$width = imagesx($im);
$height = imagesy($im);

// Verifica se a mensagem é maior do que a capacidade de armazenamento da imagem.
// Caso positivo, encerra o programa.
if (strlen($bits) > $width * $height) {
    echo "O texto é maior do que a capacidade de armazenamento da imagem\n";
    return;
}

// Cria uma nova imagem de mesmo tamanho
$new_im = imagecreatetruecolor($width, $height);

// Variável de controle de acesso ao array da sequência de bits
$bitIndex = 0;

for ($i = 0; $i < $width; $i++) {
    for ($j = 0; $j < $height; $j++) {

        // Recupera o pixel i,j da imagem 
        $pixel = imagecolorat($im, $i, $j);

        // Extrai os valores de vermelho, verde e azul do pixel
        $red = ($pixel >> 16) & 0xFF;
        $green = ($pixel >> 8) & 0xFF;
        $blue = $pixel & 0xFF;

        // Verifica se ainda exitem bits a serem armazenados
        if ($bitIndex < strlen($bits)) {

            // Gera os novos valores de vermelho, verde e azul
            $new_red =   ($red   & 0xFE) | ($bits[$bitIndex    ] ?? '0');
            $new_green = ($green & 0xFE) | ($bits[$bitIndex + 1] ?? '0');
            $new_blue =  ($blue  & 0xFE) | ($bits[$bitIndex + 2] ?? '0');
            $bitIndex += 3;

            // Gera um novo pixel com os valores das novas componentes de vermelho, verde e azul
            $new_pixel = ($new_red << 16) | ($new_green << 8) | $new_blue;

            // Se a opção -v foi passada como parâmetro co comando exibe os valores do novo pixel
            if (isset($options['v'])) {
                echo "NEW [X: $i, Y: $j] - R: $new_red, G: $new_green, B: $new_blue, New: $new_pixel\n";
            }

            // Atribui o novo pixel na imagem de saída
            imagesetpixel($new_im, $i, $j, $new_pixel);

            // Pula para a próxima iteração
            continue;
        }

        // Atribui o pixel atual na imagem de saída
        imagesetpixel($new_im, $i, $j, $pixel);
    }
}

// Gera a imagem final com a mensagem codificada
imagepng($new_im, $options['o']);
