package Dao;

import Model.Usuarios;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

public class UsuarioDao {
    public int inserir(Usuarios usuario) throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            int status = usuario.getFlStatus() == null ? 0 : usuario.getFlStatus();
            return inserir(conexao, usuario, status);
        }
    }

    public boolean existeUsuario() throws SQLException {
        return Sql.buscar("SELECT idUsuario FROM usuarios LIMIT 1", rs -> rs.getInt(1)).isPresent();
    }

    public void inserirPrimeiro(Usuarios usuario) throws SQLException {
        Sql.emTransacao(conexao -> {
            conexao.setTransactionIsolation(java.sql.Connection.TRANSACTION_SERIALIZABLE);
            if (!Sql.listar(conexao, "SELECT idUsuario FROM usuarios LIMIT 1 FOR UPDATE", rs -> rs.getInt(1)).isEmpty()) {
                throw new SQLException("Já existe um usuário cadastrado. Entre com a conta existente.");
            }
            inserir(conexao, usuario, 0);
            return null;
        });
    }

    private int inserir(Connection conexao, Usuarios usuario, int status) throws SQLException {
        if (temStatus(conexao)) {
            return Sql.inserir(conexao, "INSERT INTO usuarios (Nome, Email, Senha, flStatus) VALUES (?, ?, ?, ?)",
                    usuario.getNome(), usuario.getEmail(), usuario.getSenha(), status);
        }
        return Sql.inserir(conexao, "INSERT INTO usuarios (Nome, Email, Senha) VALUES (?, ?, ?)",
                usuario.getNome(), usuario.getEmail(), usuario.getSenha());
    }

    public boolean alterar(Usuarios usuario) throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            StringBuilder comando = new StringBuilder("UPDATE usuarios SET Nome = ?, Email = ?");
            List<Object> parametros = new ArrayList<>();
            parametros.add(usuario.getNome());
            parametros.add(usuario.getEmail());
            if (usuario.getFlStatus() != null && temStatus(conexao)) {
                comando.append(", flStatus = ?");
                parametros.add(usuario.getFlStatus());
            }
            if (usuario.getSenha() != null) {
                comando.append(", Senha = ?");
                parametros.add(usuario.getSenha());
            }
            comando.append(" WHERE idUsuario = ?");
            parametros.add(usuario.getIdUsuario());
            return Sql.executar(conexao, comando.toString(), parametros.toArray()) > 0;
        }
    }

    public boolean excluir(int id) throws SQLException {
        return Sql.executar("DELETE FROM usuarios WHERE idUsuario = ?", id) > 0;
    }

    public Optional<Usuarios> buscar(int id) throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            return Sql.buscar(conexao, consulta(temStatus(conexao)) + " WHERE idUsuario = ?", UsuarioDao::ler, id);
        }
    }

    public List<Usuarios> listar() throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            return Sql.listar(conexao, consulta(temStatus(conexao)) + " ORDER BY idUsuario", UsuarioDao::ler);
        }
    }

    public Optional<Usuarios> autenticar(String email, String senha) throws SQLException {
        try (Connection conexao = DataSource.conectar()) {
            boolean temStatus = temStatus(conexao);
            String filtroStatus = temStatus ? " AND flStatus = 0" : "";
            return Sql.buscar(conexao, consulta(temStatus) + " WHERE Email = ? AND BINARY Senha = BINARY ?"
                    + filtroStatus + " ORDER BY idUsuario LIMIT 1", UsuarioDao::ler, email, senha);
        }
    }

    private static boolean temStatus(Connection conexao) throws SQLException {
        try (PreparedStatement stmt = conexao.prepareStatement("SELECT * FROM usuarios WHERE 1 = 0");
                ResultSet resultado = stmt.executeQuery()) {
            ResultSetMetaData colunas = resultado.getMetaData();
            for (int i = 1; i <= colunas.getColumnCount(); i++) {
                if ("flStatus".equalsIgnoreCase(colunas.getColumnName(i))) {
                    return true;
                }
            }
            return false;
        }
    }

    private static String consulta(boolean temStatus) {
        String status = temStatus ? "flStatus" : "CAST(NULL AS SIGNED) AS flStatus";
        return "SELECT idUsuario, Nome, Email, " + status + " FROM usuarios";
    }

    private static Usuarios ler(ResultSet rs) throws SQLException {
        Usuarios usuario = new Usuarios();
        usuario.setIdUsuario(rs.getInt("idUsuario"));
        usuario.setNome(rs.getString("Nome"));
        usuario.setEmail(rs.getString("Email"));
        int status = rs.getInt("flStatus");
        usuario.setFlStatus(rs.wasNull() ? null : status);
        return usuario;
    }
}
