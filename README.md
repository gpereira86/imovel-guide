# Gerenciamento de Corretores

## Sobre o Projeto
Este é um sistema simples e eficiente para inclusão, edição e exclusão de corretores no banco de dados. O projeto foi desenvolvido com foco em boas práticas e organização de código, utilizando a arquitetura **MVC** para separar responsabilidades.

- <a href="https://testimovelguide.glaucopereira.com/" target="_blank">Link para acesso ao projeto já online</a><br><br>

## Tecnologias Utilizadas
- **PHP 7.4** (puro, sem frameworks ou bibliotecas externas)
- **JavaScript**
- **HTML5**
- **Bootstrap 5**
- **CSS3**

## Funcionalidades
✅ Cadastro de novos corretores  
✅ Edição de informações de corretores existentes  
✅ Exclusão segura de registros  
✅ Interface responsiva com Bootstrap  
✅ Validações no frontend e backend  

## Estrutura do Projeto
O sistema segue a arquitetura **MVC (Model-View-Controller)**, garantindo um código modular e de fácil manutenção:
- 📂 **Model** → Gerencia a comunicação com o banco de dados
- 📂 **View** → Responsável pela interface do usuário
- 📂 **Controller** → Processa requisições e conecta Model e View

## Como Executar
1. Clone este repositório
   ```bash
   git clone https://github.com/gpereira86/imovel-guide.git
   ```
2. Configure os dados do projeto como banco de dados e url no arquivo `config.php` e avalie alteração no arquivo `.htaccess`
3. Caso não tenha o composer instale, em no terminal, navegue até o caminho da raiz deste projeto e execute o comando `composer update`
4. Inicie um servidor local (Apache, Nginx ou built-in do PHP)
   ```bash
   php -S localhost:8000
   ```
   ou inicie o xaamp.
5. Acesse `http://localhost:8000` no navegador ou o correspondente no xaamp

## Contribuição
Sinta-se à vontade para contribuir com melhorias! Sugestões e PRs são bem-vindos.

---
