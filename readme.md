### google-sheets-php
Projeto criado para trabalhar com inserção de dados em planilhas do google com PHP;

# Requisitos
- PHP >= 7.3
- [Composer](https://getcomposer.org/)

# Como usar
1. Acesse o [Console do Google - API](https://console.cloud.google.com/apis/dashboard), ative a API **Google Sheets API**
1. Acesse o [Console do Google - Credênciais](https://console.cloud.google.com/apis/credentials), crie uma credencial e copie o código
1. Acesse o [Console do Google - IAM ADMIN](https://console.cloud.google.com/iam-admin/serviceaccounts), crie um novo projeto, acesse-o, navegue até guia **chaves** e adicione uma chave json, baixe-a e separe.
1. Clone o repositório, insira a chave dentro do repositório com o nome: **auth.json**.
1. Execute o comando `composer install` para instalar as dependências
1. Crie e configure a sua planilha em modo público e modo editor para quem possuir o link;

Agora basta realizar os testes...
Usei uma requisição AJAX para estar efetuando a integração via API

`/req.html`
```JS
window.addEventListener("load", () => {
            let result = $.post('http://localhost/google-planilhas/', {
                planilha_id: '1-JrsMcc7DpPnoorb0q2PdsaiCNwtIZ_hduBrbQuYjq0ko8',
                data: [
                    "Thiago Sales",
                    "thsales1997",
                    "27/04/1997",
                    "Rio de Janeiro"
                ]
            }).done((result) => {
                console.log(result)
            })
        })
```
Obs: necessário passar o id da planilha e o array contendo os valores das colunas.
O id da planilha você obtem na url, após o /d/
```txt
https://docs.google.com/spreadsheets/d/
1-JrsMcc7DpPnoorb0q2PdsaiCNwtIZ_hduBrbQuYjq0ko8 // < Um exemplo
/
```

### Licença
MIT