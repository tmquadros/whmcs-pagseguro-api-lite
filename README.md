# whmcs-pagseguro-api-lite
Este módulo visa implementar as APIs de Pagamentos e de Notificações do PagSeguro para o sistema gerenciador de hospedagens WHMCS.
Já existem outros módulos que fazem esta mesma função, porém todos são pagos e com código fonte fechado, o que impossibilita a auditoria do código.

Instalação
----------
 - Envie o arquivo "pagseguroapilite.php" para o diretório "modules/gateways" do seu WHMCS.
 - Acesse através do Painel de Administração o menu "Opções" -> "Pagamentos" -> "Portais para Pagamentos"
 - Ative o módulo "PagSeguro API Lite (SuporteWHMCS.Com.Br)"
 - Preencha, nas opções do módulo, o email e o token da sua conta PagSeguro. Se desejar, mude também o campo "Exibir o nome" para somente "PagSeguro", este é o nome que será exibido para seus clientes

O que este módulo oferece?
--------------------------
- Encaminha de forma segura as informações da cobrança ao PagSeguro.
- Recebe de forma segura as notificações de pagamento.
- É gratuito para utilização e ainda permite a auditoria do código fonte.

O que este módulo não oferece?
------------------------------
- Opções de customização: se desejar modificar a aparência ou comportamento do módulo, precisará editar o seu código fonte. Em breve estará disponível um fork totalmente configurável.
- Tratamento de erros: como os dados passados para o PagSeguro são extremamente limitados e independem de dados informados pelo usuário, as duas únicas possibilidades de erros ocorrerem seria pela configuração incorreta (Email ou token inválidos) ou falha na comunicação com os servidores do PagSeguro. Neste caso, apenas é exibida ao usuário uma mensagem informando que ocorreu um erro na comunicação. Mais informações podem ser obtidas através do "Log dos Portais".
- Tratamento de disputas e estornos: tais operações, que representam um percentual ínfimo das transações totais, devem ser tratadas manualmente.

Dúvidas, sugestões, bugs?
-------------------------
Entre em contato através do email: pagseguro@suportewhmcs.com.br

Changelog
---------
1.0
- Versão inicial. Integração com APIs de Pagamentos e Notificações do PagSeguro.
