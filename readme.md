### [google-sheets-php](https://github.com/tsalesproductions/google-sheets-php)
Projeto criado para trabalhar com inserção de dados em planilhas do google com PHP; 
E ADAPTADO PARA CRIAÇÃO DE CATEGORIAS NA VTEX;

# Requisitos
- PHP >= 7.3
- [Composer](https://getcomposer.org/)

# OBS
1. A planilha deve estar no modo público
2. A api da VTEX tem cache de 5~10 minutos para retornar as novas categorias criadas, não rode o script novamente antes desse tempo, caso contrário, irá duplicar as categorias.
3. Seguir o modelo de planilha: `Breve`
4. O foi comentado informando como foi criado, e a lógica por cima.

# Como usar
1. Abra o `index.php`
1. Procure por `sheet_id` e troque pelo id da sua planilha, veja o exemplo abaixo, pegando a id via URL
```txt
https://docs.google.com/spreadsheets/d/
1-JrsMcc7DpPnoorb0q2PdsaiCNwtIZ_hduBrbQuYjq0ko8 // < ESTE É O ID
```
1. Procure por `vtex_host` `vtex_app_id` `vtex_app_token` e troque as credênciais dos clientes
1. A linha `sheet_range_find` É responsável por pegar as colunas. Entenda abaixo:
```text
# QUANTIDADE DE COLUNAS QUE A TABELA IRÁ BUSCAR: 
# A2:D999 = QUERO QUE ME TRAGA AS LINHAS DE A2(PARA IGNORAR OS TÍTULOS) ATÉ O D(MINHA ÚLTIMA COLUNA). 
# ALÉM DISSO QUERO QUE ELE ME TRAGA OS PRIMEIROS 999 RESULTADOS DESSE DOCUMENTO
define("sheet_range_find", 'A6:F999');
```
1. Após trocar os dados, rode uma vez o index.php e confira se irá funcionar corretamente.

###Licença
MIT