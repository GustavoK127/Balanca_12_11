package Dao;

import Model.Orcamentos;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public class OrcamentoDao {
    private static final String SELECT = """
            SELECT o.idOrcamento, o.dtAprovacao, o.idEngenheiroResponsavel, e.dsNome AS nomeResponsavel
            FROM orcamentos o LEFT JOIN engenheiros e ON e.idEngenheiro = o.idEngenheiroResponsavel
            """;

    public int inserir(Orcamentos orcamento) throws SQLException {
        return Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "engenheiros", "idEngenheiro", orcamento.idEngenheiroResponsavel());
            return Sql.inserir(conexao, "INSERT INTO orcamentos (dtAprovacao, idEngenheiroResponsavel) VALUES (?, ?)",
                    orcamento.dtAprovacao(), orcamento.idEngenheiroResponsavel());
        });
    }

    public boolean alterar(Orcamentos orcamento) throws SQLException {
        return Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "engenheiros", "idEngenheiro", orcamento.idEngenheiroResponsavel());
            return Sql.executar(conexao, """
                    UPDATE orcamentos SET dtAprovacao = ?, idEngenheiroResponsavel = ? WHERE idOrcamento = ?
                    """, orcamento.dtAprovacao(), orcamento.idEngenheiroResponsavel(), orcamento.idOrcamento()) > 0;
        });
    }

    public boolean excluir(int id) throws SQLException {
        return Sql.emTransacao(conexao -> {
            if (Sql.listar(conexao, "SELECT idOrcamento FROM orcamentos WHERE idOrcamento = ? FOR UPDATE",
                    rs -> rs.getInt(1), id).isEmpty()) {
                return false;
            }
            Sql.executar(conexao, "DELETE FROM orcamentos_obras WHERE idOrcamento = ?", id);
            return Sql.executar(conexao, "DELETE FROM orcamentos WHERE idOrcamento = ?", id) > 0;
        });
    }

    public Optional<Orcamentos> buscar(int id) throws SQLException {
        return Sql.buscar(SELECT + " WHERE o.idOrcamento = ?", OrcamentoDao::ler, id);
    }

    public List<Orcamentos> listar() throws SQLException {
        return Sql.listar(SELECT + " ORDER BY o.idOrcamento", OrcamentoDao::ler);
    }

    private static Orcamentos ler(ResultSet rs) throws SQLException {
        return new Orcamentos(rs.getInt("idOrcamento"), rs.getObject("dtAprovacao", LocalDate.class),
                rs.getInt("idEngenheiroResponsavel"), rs.getString("nomeResponsavel"));
    }
}
