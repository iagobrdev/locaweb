
# LocaWeb - Caixa Eletrônico

Esse é meu teste lógico para a vaga de programador Sênior Backend PHP na empresa  [LocaWeb](https://www.locaweb.com.br/).

## [](https://github.com/iagobrdev/locaweb#tecnologias-utilizadas)Tecnologias Utilizadas

-   Backend
    
-   PHP
    
-   PHPUnit
    
-   Laravel
    
-   Swagger
    
-   Docker
    

Foi utilizado a estrutura de containers (Docker) na aplicação, para realizar o build do projeto basta abrir o terminal e navegar até o diretório do mesmo. Logo após é necessário executar o comando  `docker compose up -d`  que irá criar o container e baixar as dependências.

Foi utilizado o PHPUnit para realizar testes unitários solicitados na documentação fornecida pelo recrutador. Para isso, basta executar no terminal o comando  `docker compose exec app php artisan test`  que será exibido o resultado.

[![](https://github.com/iagobrdev/locaweb/raw/main/assets/tests.png)](https://github.com/iagobrdev/locaweb/blob/main/assets/tests.png)

Existem duas formas de testar a aplicação. Através do  **terminal**  ou através da  **api**  que foi desenvolvida também.

Para realizar os testes através do terminal, precisam ser informadas as entradas dentro do arquivo  **input.json**  que se encontra na raíz do diretório.

[![](https://github.com/iagobrdev/locaweb/raw/main/assets/input.png)](https://github.com/iagobrdev/locaweb/blob/main/assets/input.png)Obs.: Podem ser informadas mais de uma entrada como no exemplo acima.

Após salvar as alterações do arquivo, execute  `docker compose exec app sh -c "php artisan caixa < input.json"`  que os testes serão realizados e exibidos para o usuário  _(stdout)_  como no exemplo abaixo.

[![](https://github.com/iagobrdev/locaweb/raw/main/assets/output.png)](https://github.com/iagobrdev/locaweb/blob/main/assets/output.png)

Obs.: Para realizar o próximo teste, basta alterar a(s) entrada(s) no arquivo  **input.json**, salvar e executar novamente o comando.

A outra forma de testar a aplicação é através da  **api**. Para isso, basta acessar o endereço  **[http://localhost:8000/docs/](http://localhost:8000/docs/)**  que contém toda a documentação criada com o Swagger.

[![](https://github.com/iagobrdev/locaweb/raw/main/assets/api.png)](https://github.com/iagobrdev/locaweb/blob/main/assets/api.png)

Os testes podem ser realizados diretamente pelo Browzer através do método  _**POST**_, de uma forma mais prática. Para isso, basta escolher a opção ***post / caixa***  e logo em seguida clicar no botão  _**Try it out**_.

[![](https://github.com/iagobrdev/locaweb/raw/main/assets/api_input.png)](https://github.com/iagobrdev/locaweb/blob/main/assets/api_input.png)

Será exibida uma interface de entrada com um json padrão, mas o usuário pode alterá-lo para realizar os testes. Ao clicar no botão  _**Execute**_  a api retornará o json  _(stdout)_  e o código de resposta (**200**  = Sucesso,  **400**  = Erro).

[![](https://github.com/iagobrdev/locaweb/raw/main/assets/api_output.png)](https://github.com/iagobrdev/locaweb/blob/main/assets/api_output.png)
