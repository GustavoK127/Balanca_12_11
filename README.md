# Balança-Mas-Não-Cai

Sistema acadêmico de gerenciamento de obras de uma construtora. O projeto inclui uma versão web em **PHP** e o desenvolvimento de uma versão desktop em **Java Swing** com acesso ao **MySQL** via JDBC.

## Versão web

A versão web usa PHP, HTML5 e CSS3. O código inclui autenticação, menus de manutenção e cadastros de engenheiros, fases, funções e usuários. [Código da versão web](Balanca/balanacca/)

## Desenvolvimento em Java

A versão desktop é desenvolvida no NetBeans com Java 21 e Swing. O pacote `Dao` reúne a conexão JDBC com o MySQL e as operações de inclusão, consulta, alteração e exclusão de obras, engenheiros, fases, funções, usuários e orçamentos.

Os DAOs também gerenciam a alocação de engenheiros nas obras, as datas do histórico de fases e as previsões de gastos por obra, com valores previstos e realizados. [Pacote Dao da aplicação Java](Balanca_25_11/src/Dao/)

## Uso do pacote Dao

O código Java publicado aqui corresponde à camada de acesso a dados. Para integrá-la, use o pacote `Model` do projeto Java e adicione o MySQL Connector/J às dependências. A conexão usa o banco `balanca_mas_nao_cai` por padrão.

| Variável de ambiente | Valor |
| --- | --- |
| `BALANCA_DB_URL` | URL JDBC do servidor, como `jdbc:mysql://localhost:3306/balanca_mas_nao_cai` |
| `BALANCA_DB_USUARIO` | Usuário do MySQL |
| `BALANCA_DB_SENHA` | Senha do MySQL |

## Contexto acadêmico

O projeto aplica desenvolvimento web e desktop, modelagem de banco de dados relacional, autenticação e operações CRUD a um cenário de controle de obras.
