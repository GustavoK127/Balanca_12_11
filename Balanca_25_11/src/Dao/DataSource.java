package Dao;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public final class DataSource {
    private static volatile Credenciais credenciaisDaSessao;

    private DataSource() {
    }

    public static Connection conectar() throws SQLException {
        Credenciais credenciais = credenciaisDaSessao;
        if (credenciais == null) {
            credenciais = new Credenciais(usuarioConfigurado(), configuracao("BALANCA_DB_SENHA", ""));
        }
        return abrir(credenciais);
    }

    public static String usuarioConfigurado() {
        return configuracao("BALANCA_DB_USUARIO", "root");
    }

    public static void autenticarSessao(String usuario, char[] senha) throws SQLException {
        Credenciais novasCredenciais = new Credenciais(usuario, new String(senha));
        abrir(novasCredenciais).close();
        credenciaisDaSessao = novasCredenciais;
    }

    private static Connection abrir(Credenciais credenciais) throws SQLException {
        String url = configuracao("BALANCA_DB_URL",
                "jdbc:mysql://localhost:3306/balanca_mas_nao_cai?connectTimeout=5000&socketTimeout=10000");
        return DriverManager.getConnection(url, credenciais.usuario(), credenciais.senha());
    }

    private static String configuracao(String nome, String padrao) {
        String valor = System.getProperty(nome);
        if (valor == null) {
            valor = System.getenv(nome);
        }
        return valor == null ? padrao : valor;
    }

    private record Credenciais(String usuario, String senha) {
    }
}
