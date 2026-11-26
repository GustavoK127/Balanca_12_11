package Dao;

import Model.ObrasFases;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public class ObrasFasesDao {
    private static final String SELECT = """
            SELECT v.idObra, v.idFase, v.dtIniciofase, v.dtTerminoFase,
                   f.dsFase AS descricaoFase
            FROM obras_fases v
            LEFT JOIN fases f ON f.idFase = v.idFase
            """;

    public void inserir(ObrasFases registro) throws SQLException {
        Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "obras", "idObra", registro.idObra());
            Sql.exigirRegistro(conexao, "fases", "idFase", registro.idFase());
            Sql.executar(conexao, """
                    INSERT INTO obras_fases (idObra, idFase, dtIniciofase, dtTerminoFase)
                    VALUES (?, ?, ?, ?)
                    """, registro.idObra(), registro.idFase(),
                    registro.dtInicioFase(), registro.dtTerminoFase());
            return null;
        });
    }

    public boolean alterar(ObrasFases registro) throws SQLException {
        return Sql.executar("""
                UPDATE obras_fases SET dtIniciofase = ?, dtTerminoFase = ?
                WHERE idObra = ? AND idFase = ?
                """, registro.dtInicioFase(), registro.dtTerminoFase(),
                registro.idObra(), registro.idFase()) > 0;
    }

    public boolean excluir(int idObra, int idFase) throws SQLException {
        return Sql.executar("DELETE FROM obras_fases WHERE idObra = ? AND idFase = ?",
                idObra, idFase) > 0;
    }

    public Optional<ObrasFases> buscar(int idObra, int idFase) throws SQLException {
        return Sql.buscar(SELECT + " WHERE v.idObra = ? AND v.idFase = ?",
                ObrasFasesDao::ler, idObra, idFase);
    }

    public List<ObrasFases> listar(int idObra) throws SQLException {
        return Sql.listar(SELECT + " WHERE v.idObra = ? ORDER BY v.idFase", ObrasFasesDao::ler, idObra);
    }

    private static ObrasFases ler(ResultSet rs) throws SQLException {
        return new ObrasFases(rs.getInt("idObra"), rs.getInt("idFase"),
                rs.getObject("dtIniciofase", LocalDate.class),
                rs.getObject("dtTerminoFase", LocalDate.class),
                rs.getString("descricaoFase"));
    }
}
