# Arquitetura de Software usando PHP

Estudo realizado utilizando como base o curso [PHP BR - Arquitetura de software](https://www.youtube.com/playlist?list=PLw9GPuhnwsdOEBCDA1uN0VImix2yJd2zO).

## Arquitetura de Software - Definição
Pode ser definida como a forma de divisão do sistema em componentes, na organização desses componentes e no modo como esses componentes se comunicam entre si.

## Os benefícios de uma boa arquitetura
* Isolamento de responsabilidades;
* Criação de sistemas testáveis;
* Escalabilidade;
* Legibilidade do sistema pela equipe de desenvolvimento;
* Longevidade na manutenção de um software;

### MVC
Criado em 1979 por Trygve Reenskaug;

Divisão de responsabilidade em 3 camadas:
    - MODEL
    Camada responsável pelas regras de negócio da aplicação.
    - VIEW
    Camada responsável pela interação entre o usuário e o sistema.
    - CONTROLLER
    Camada responsável pelo fluxo do sistema, é ela que interliga as solicitações vindas da view com as regras de negócio contidas no model.
    
### Sobre o projeto desenvolvido
O projeto implementado no curso é um cadastro de pessoas em um evento, mas para tirar maior proveito dos ensinamentos criei uma aplicação de cadastro de tarefas com base nos ensinamentos do vídeo.
Foram implementadas diversas maneiras de implementar uma mesma aplicação, desde o padrão procedural até o MVC.

Para ter acesso aos arquivos navegue entre as branches criadas:
* [v1.0 - bad code](https://github.com/felipeverse/PHP-Arquitetura-De-Software/tree/v1.0-bad_code)
