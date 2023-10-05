# Shadow

Ferramenta de CLI escrita em PHP que realiza esteganografia em imagens PNG.

A ferramenta é composta por dois scripts PHP:

- `shadow_encoder.php`: Script que realiza a codificação de um texto em uma imagem;
- `shadow_decoder.php`: Script que realiza a decodificação de uma imagem para um texto.

## Codificando um texto para uma imagem

Para codificar um texto para uma imagem execute o comando a seguir:

```bash
php shadow_encoder.php -i imagem.png -t texto.txt -o nova_imagem.png
```

Onde:

- `-i imagem.png`: É o caminho da imagem que será utilizada para armazenar o texto via esteganografia;
- `-t texto.txt`: É o caminho do arquivo de texto que contém o conteúdo a ser codificado na imagem;
- `-o nova_imagem.png`: É o caminho da nova imagem que será gerada após o processo de codificação.

Exemplo de uso:

```bash
php shadow_encoder.php -i lenna.png -t texto.txt -o nova_lenna.png
```

## Decodificando uma imagem para um texto

Para decodificar a imagem para texto execute o comando a seguir:

```bash
php shadow_decoder.php -i imagem_codificada.png -t texto_decodificado.txt    
```

Onde:

- `-i imagem.png`: É o caminho da imagem que será utilizada no processo de decodificação;
- `-t texto.txt`: É o caminho do arquivo de texto que contém o conteúdo a ser codificado na imagem.

Exemplo de uso:

```bash
php shadow_decoder.php -i nova_lenna.png -t poema.txt  
```

## Motivação

Participei como palestrante do PHPeste 2023 com a palestra **Esteganografia: A arte de ocultar uma mensagem dentro de outra, com PHP!** e o código deste repositório foi implementado para exemplificar a técnica de esteganografia.

Os lsides da palestra podem ser encontrados [aqui](https://linktr.ee/profbrunolopesce)
