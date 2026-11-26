package Dao;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

final class Sql {
    @FunctionalInterface
    interface Mapeador<T> {
        T ler(ResultSet resultado) throws SQLException;
    }

    @FunctionalInterface
    interface Operacao<T> {
        T executar(Connection conexao) throws SQLException;
    }

    private Sql() {
    }

    static <T> T emTransacao(Operacao<T> operacao) throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            conexao.setAutoCommit(false);
            try {
                T resultado = operacao.executar(conexao);
                conexao.commit();
                return resultado;
            } catch (SQLException | RuntimeException erro) {
                try {
                    conexao.rollback();
                } catch (SQLException erroRollback) {
                    erro.addSuppressed(erroRollback);
                }
                throw erro;
            }
        }
    }

    static int inserir(String comando, Object... parametros) throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            return inserir(conexao, comando, parametros);
        }
    }

    static int inserir(Connection conexao, String comando, Object... parametros) throws SQLException {
        try (PreparedStatement stmt = conexao.prepareStatement(comando, Statement.RETURN_GENERATED_KEYS)) {
            preencher(stmt, parametros);
            stmt.executeUpdate();
            try (ResultSet resultado = stmt.getGeneratedKeys()) {
                if (!resultado.next()) {
                    throw new SQLException("O banco não retornou o código gerado.");
                }
                return resultado.getInt(1);
            }
        }
    }

    static int executar(String comando, Object... parametros) throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            return executar(conexao, comando, parametros);
        }
    }

    static int executar(Connection conexao, String comando, Object... parametros) throws SQLException {
        try (PreparedStatement stmt = conexao.prepareStatement(comando)) {
            preencher(stmt, parametros);
            return stmt.executeUpdate();
        }
    }

    static <T> List<T> listar(String comando, Mapeador<T> mapeador, Object... parametros)
            throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            return listar(conexao, comando, mapeador, parametros);
        }
    }

    static <T> List<T> listar(Connection conexao, String comando, Mapeador<T> mapeador,
            Object... parametros) throws SQLException {
        List<T> registros = new ArrayList<>();
        try (PreparedStatement stmt = conexao.prepareStatement(comando)) {
            preencher(stmt, parametros);
            try (ResultSet resultado = stmt.executeQuery()) {
                while (resultado.next()) {
                    registros.add(mapeador.ler(resultado));
                }
            }
        }
        return registros;
    }

    static <T> Optional<T> buscar(String comando, Mapeador<T> mapeador, Object... parametros)
            throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            return buscar(conexao, comando, mapeador, parametros);
        }
    }

    static <T> Optional<T> buscar(Connection conexao, String comando, Mapeador<T> mapeador,
            Object... parametros) throws SQLException {
        List<T> registros = listar(conexao, comando, mapeador, parametros);
        return registros.stream().findFirst();
    }

    static boolean excluirSemVinculos(String comando, String tabela, String coluna, int id)
            throws SQLException {
        return emTransacao(conexao -> {
            if (executar(conexao, comando, id) > 0) {
                return true;
            }
            if (!listar(conexao, "SELECT " + coluna + " FROM " + tabela + " WHERE " + coluna + " = ?",
                    rs -> rs.getInt(1), id).isEmpty()) {
                throw new SQLException("O registro está vinculado a outro cadastro.", "23000");
            }
            return false;
        });
    }

    static void exigirRegistro(Connection conexao, String tabela, String coluna, int id) throws SQLException {
        String consulta = "SELECT " + coluna + " FROM " + tabela + " WHERE " + coluna + " = ? FOR UPDATE";
        if (listar(conexao, consulta, rs -> rs.getInt(1), id).isEmpty()) {
            throw new SQLException("O registro selecionado já não existe.", "23000");
        }
    }

    private static void preencher(PreparedStatement stmt, Object[] parametros) throws SQLException {
        for (int i = 0; i < parametros.length; i++) {
            stmt.setObject(i + 1, parametros[i]);
        }
    }
}
