package Dao;

import Model.Fases;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import java.util.Optional;

public class FasesDao {
    private static final String SELECT = "SELECT idFase, dsFase FROM fases";

    public int inserir(Fases registro) throws SQLException {
        return Sql.inserir("INSERT INTO fases (dsFase) VALUES (?)",
                registro.getDsFase());
    }

    public boolean alterar(Fases registro) throws SQLException {
        return Sql.executar("UPDATE fases SET dsFase = ? WHERE idFase = ?",
                registro.getDsFase(), registro.getIdFase()) > 0;
    }

    public boolean excluir(int id) throws SQLException {
        return Sql.excluirSemVinculos("""
                DELETE FROM fases WHERE idFase = ?
                  AND NOT EXISTS (SELECT 1 FROM obras_fases WHERE obras_fases.idFase = fases.idFase)
                """, "fases", "idFase", id);
    }

    public Optional<Fases> buscar(int id) throws SQLException {
        return Sql.buscar(SELECT + " WHERE idFase = ?", FasesDao::ler, id);
    }

    public List<Fases> listar() throws SQLException {
        return Sql.listar(SELECT + " ORDER BY idFase", FasesDao::ler);
    }

    private static Fases ler(ResultSet rs) throws SQLException {
        Fases registro = new Fases();
        registro.setIdFase(rs.getInt("idFase"));
        registro.setDsFase(rs.getString("dsFase"));
        return registro;
    }
}
