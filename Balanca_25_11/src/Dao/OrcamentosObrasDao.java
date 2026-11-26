package Dao;

import Model.OrcamentosObras;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public class OrcamentosObrasDao {
    private static final String SELECT = """
            SELECT v.idOrcamento, v.idObra, v.dtInicio, v.dtFim, v.vlPrevisto, v.vlRealizado,
                   o.Descricao AS descricaoObra
            FROM orcamentos_obras v
            LEFT JOIN obras o ON o.idObra = v.idObra
            """;

    public void inserir(OrcamentosObras registro) throws SQLException {
        Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "orcamentos", "idOrcamento", registro.idOrcamento());
            Sql.exigirRegistro(conexao, "obras", "idObra", registro.idObra());
            Sql.executar(conexao, """
                    INSERT INTO orcamentos_obras (idOrcamento, idObra, dtInicio, dtFim, vlPrevisto, vlRealizado)
                    VALUES (?, ?, ?, ?, ?, ?)
                    """, registro.idOrcamento(), registro.idObra(),
                    registro.dtInicio(), registro.dtFim(), registro.vlPrevisto(), registro.vlRealizado());
            return null;
        });
    }

    public boolean alterar(OrcamentosObras registro) throws SQLException {
        return Sql.executar("""
                UPDATE orcamentos_obras SET dtInicio = ?, dtFim = ?, vlPrevisto = ?, vlRealizado = ?
                WHERE idOrcamento = ? AND idObra = ?
                """, registro.dtInicio(), registro.dtFim(), registro.vlPrevisto(), registro.vlRealizado(),
                registro.idOrcamento(), registro.idObra()) > 0;
    }

    public boolean excluir(int idOrcamento, int idObra) throws SQLException {
        return Sql.executar("DELETE FROM orcamentos_obras WHERE idOrcamento = ? AND idObra = ?",
                idOrcamento, idObra) > 0;
    }

    public Optional<OrcamentosObras> buscar(int idOrcamento, int idObra) throws SQLException {
        return Sql.buscar(SELECT + " WHERE v.idOrcamento = ? AND v.idObra = ?",
                OrcamentosObrasDao::ler, idOrcamento, idObra);
    }

    public List<OrcamentosObras> listar(int idOrcamento) throws SQLException {
        return Sql.listar(SELECT + " WHERE v.idOrcamento = ? ORDER BY v.idObra", OrcamentosObrasDao::ler, idOrcamento);
    }

    private static OrcamentosObras ler(ResultSet rs) throws SQLException {
        return new OrcamentosObras(rs.getInt("idOrcamento"), rs.getInt("idObra"),
                rs.getObject("dtInicio", LocalDate.class),
                rs.getObject("dtFim", LocalDate.class),
                rs.getBigDecimal("vlPrevisto"),
                rs.getBigDecimal("vlRealizado"),
                rs.getString("descricaoObra"));
    }
}
