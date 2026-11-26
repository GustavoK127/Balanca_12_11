package Dao;

import Model.Funcoes;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import java.util.Optional;

public class FuncoesDao {
    private static final String SELECT = "SELECT idFuncao, dsFuncao FROM funcoes";

    public int inserir(Funcoes registro) throws SQLException {
        return Sql.inserir("INSERT INTO funcoes (dsFuncao) VALUES (?)",
                registro.getDsFuncao());
    }

    public boolean alterar(Funcoes registro) throws SQLException {
        return Sql.executar("UPDATE funcoes SET dsFuncao = ? WHERE idFuncao = ?",
                registro.getDsFuncao(), registro.getIdFuncao()) > 0;
    }

    public boolean excluir(int id) throws SQLException {
        return Sql.excluirSemVinculos("""
                DELETE FROM funcoes WHERE idFuncao = ?
                  AND NOT EXISTS (SELECT 1 FROM obras_engenheiros WHERE obras_engenheiros.idFuncao = funcoes.idFuncao)
                """, "funcoes", "idFuncao", id);
    }

    public Optional<Funcoes> buscar(int id) throws SQLException {
        return Sql.buscar(SELECT + " WHERE idFuncao = ?", FuncoesDao::ler, id);
    }

    public List<Funcoes> listar() throws SQLException {
        return Sql.listar(SELECT + " ORDER BY idFuncao", FuncoesDao::ler);
    }

    private static Funcoes ler(ResultSet rs) throws SQLException {
        Funcoes registro = new Funcoes();
        registro.setIdFuncao(rs.getInt("idFuncao"));
        registro.setDsFuncao(rs.getString("dsFuncao"));
        return registro;
    }
}
