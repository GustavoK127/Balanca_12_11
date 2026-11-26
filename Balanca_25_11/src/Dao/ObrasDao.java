package Dao;

import Model.Obras;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public class ObrasDao {
    private static final String SELECT = """
            SELECT o.idObra, o.Descricao, o.dtInicio, o.dtFim, o.idEngenheiro, o.nrCrea,
                   o.dtDdmissaoEngenheiro, e.dsNome AS nomeResponsavel
            FROM obras o LEFT JOIN engenheiros e ON e.idEngenheiro = o.idEngenheiro
            """;

    public int inserir(Obras obra) throws SQLException {
        return Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "engenheiros", "idEngenheiro", obra.getIdEngenheiro());
            int id = Sql.inserir(conexao, """
                    INSERT INTO obras (Descricao, dtInicio, dtFim, idEngenheiro, nrCrea, dtDdmissaoEngenheiro)
                    VALUES (?, ?, ?, ?, ?, ?)
                    """, obra.getDescricao(), obra.getDtInicio(), obra.getDtFim(), obra.getIdEngenheiro(),
                    obra.getNrCrea(), obra.getDtDdmissaoEngenheiro());

            Sql.executar(conexao, "INSERT INTO obras_fases (idObra, idFase) SELECT ?, idFase FROM fases", id);
            return id;
        });
    }

    public boolean alterar(Obras obra) throws SQLException {
        return Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "engenheiros", "idEngenheiro", obra.getIdEngenheiro());
            return Sql.executar(conexao, """
                    UPDATE obras SET Descricao = ?, dtInicio = ?, dtFim = ?, idEngenheiro = ?,
                        nrCrea = ?, dtDdmissaoEngenheiro = ? WHERE idObra = ?
                    """, obra.getDescricao(), obra.getDtInicio(), obra.getDtFim(), obra.getIdEngenheiro(),
                    obra.getNrCrea(), obra.getDtDdmissaoEngenheiro(), obra.getIdObra()) > 0;
        });
    }

    public boolean excluir(int id) throws SQLException {
        return Sql.emTransacao(conexao -> {
            if (Sql.listar(conexao, "SELECT idObra FROM obras WHERE idObra = ? FOR UPDATE",
                    rs -> rs.getInt(1), id).isEmpty()) {
                return false;
            }
            if (!Sql.listar(conexao, "SELECT idOrcamento FROM orcamentos_obras WHERE idObra = ? LIMIT 1",
                    rs -> rs.getInt(1), id).isEmpty()) {
                throw new SQLException("A obra está vinculada a um orçamento.", "23000");
            }
            Sql.executar(conexao, "DELETE FROM obras_engenheiros WHERE idObra = ?", id);
            Sql.executar(conexao, "DELETE FROM obras_fases WHERE idObra = ?", id);
            return Sql.executar(conexao, "DELETE FROM obras WHERE idObra = ?", id) > 0;
        });
    }

    public Optional<Obras> buscar(int id) throws SQLException {
        return Sql.buscar(SELECT + " WHERE o.idObra = ?", ObrasDao::ler, id);
    }

    public List<Obras> listar() throws SQLException {
        return Sql.listar(SELECT + " ORDER BY o.idObra", ObrasDao::ler);
    }

    private static Obras ler(ResultSet rs) throws SQLException {
        Obras obra = new Obras();
        obra.setIdObra(rs.getInt("idObra"));
        obra.setDescricao(rs.getString("Descricao"));
        obra.setDtInicio(rs.getObject("dtInicio", LocalDate.class));
        obra.setDtFim(rs.getObject("dtFim", LocalDate.class));
        obra.setIdEngenheiro(rs.getInt("idEngenheiro"));
        obra.setNrCrea(rs.getString("nrCrea"));
        obra.setDtDdmissaoEngenheiro(rs.getObject("dtDdmissaoEngenheiro", LocalDate.class));
        obra.setNomeResponsavel(rs.getString("nomeResponsavel"));
        return obra;
    }
}
