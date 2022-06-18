Essa APLICAÇÂO tem como intuito para servir para a comunidade um intermedirio entre o MercadoLivre e a aplicação, e claro para ser reaproveitado seu codigo fonte ou pelo menos alguma parte :)

Observações iniciais para quem quiser rodar a aplicação localmente:
    Para iniciar com a utilização é necessario rodar o Migrate do Laravel para a criação do banco de dados, e para isso é importante que o .env esteja préviamente configurado.

    Importante que voce tenha sua propia central.dev do mercado livre e configure da seguinte forma no arquivo .env

    APP_ID=
    YOUR_URL=
    SECRET_KEY=

    Todas essas infomrações acima a API do mercadolivre irá disponibilizar pra voce de forma simples, porem um pouco burocratica vizando sua segurança. Mas vá na fé, tudo vai dar certo :)

Links para a utilização:
    http://187.109.230.63:8000/api/auth/get_link - Usar via POST com um paramentro STATE com um codigo aleatorio acima de 16 e abaixo de 254 caracteres para obterção de um LINK para usar WEB e fazer a atenticação da sua conta
    
    Use o link recebido no naveador e receber o refresh_token(guarde que vai precisar para as futuras requisções) Lembrando que essa informação ficará tambem guardada em meu banco de dados.
        O refresh_token do mercado livre será usado para atualizar o acess_token, o qual será usado em todas as requisições a partir de agora, e ele tem um tempo de expiração de 6 horas. O refresh_token nada mais serve para fazermos uma requisição para atualizar o acess_token, então por segurança esse será o unico momento que o sistema liberará o refresh_token para voce utilizador, então o guarde com sabedoria, caso voce o perder, nao tem problema, basta voce fazer uma nova autenticação com um novo STATE(ou seja dê um reboot).

        Outro aviso importante: Por segurança o mercado livre passou a permitir apenas o uso do protocolo HTTPS, então o link gerado pelo sistema virá com o HTTPS, mas como meu ambiente é local, nao posssui um SSL configurado, ou seja, será necessario usar o LINK do mercadolivre com o HTTPS, e depois que ele retornar com token, basta voce manualmente redirecionar para o HTTP para o sistema computa o reotorno e proseguir a rotina.
 

    