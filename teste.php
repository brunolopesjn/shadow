<?php


$mensagem = file_get_contents("texto.txt");

$tamanhoMensagem = strlen($mensagem);
$tamanhoCodificado = pack('N', $tamanhoMensagem);
$mensagemCodificada = $tamanhoCodificado . $mensagem;

$bits = '';
foreach (str_split($mensagemCodificada) as $char) {
    $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
}

print_r(bin2hex($mensagemCodificada));