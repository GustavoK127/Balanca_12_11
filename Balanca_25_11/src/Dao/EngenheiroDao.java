package Dao;

import Model.Engenheiros;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public class EngenheiroDao {
    private static final String SELECT = "SELECT idEngenheiro, dsNome, nrCREA, dtAdmissao FROM engenheiros";

    public int inserir(Engenheiros registro) throws SQLException {
        return Sql.inserir("INSERT INTO engenheiros (dsNome, nrCREA, dtAdmissao) VALUES (?, ?, ?)",
                registro.getDsNome(), registro.getNrCREA(), registro.getDtAdmissao());
    }

    public boolean alterar(Engenheiros registro) throws SQLException {
        return Sql.executar("UPDATE engenheiros SET dsNome = ?, nrCREA = ?, dtAdmissao = ? WHERE idEngenheiro = ?",
                registro.getDsNome(), registro.getNrCREA(), registro.getDtAdmissao(), registro.getIdEngenheiro()) > 0;
    }

    public boolean excluir(int id) throws SQLException {
        return Sql.excluirSemVinculos("""
                DELETE FROM engenheiros WHERE idEngenheiro = ?
                  AND NOT EXISTS (SELECT 1 FROM obras WHERE obras.idEngenheiro = engenheiros.idEngenheiro)
                  AND NOT EXISTS (SELECT 1 FROM obras_engenheiros WHERE obras_engenheiros.idEngenheiro = engenheiros.idEngenheiro)
                  AND NOT EXISTS (SELECT 1 FROM orcamentos WHERE orcamentos.idEngenheiroResponsavel = engenheiros.idEngenheiro)
                """, "engenheiros", "idEngenheiro", id);
    }

    public Optional<Engenheiros> buscar(int id) throws SQLException {
        return Sql.buscar(SELECT + " WHERE idEngenheiro = ?", EngenheiroDao::ler, id);
    }

    public List<Engenheiros> listar() throws SQLException {
        return Sql.listar(SELECT + " ORDER BY idEngenheiro", EngenheiroDao::ler);
    }

    private static Engenheiros ler(ResultSet rs) throws SQLException {
        Engenheiros registro = new Engenheiros();
        registro.setIdEngenheiro(rs.getInt("idEngenheiro"));
        registro.setDsNome(rs.getString("dsNome"));
        registro.setNrCREA(rs.getString("nrCREA"));
        registro.setDtAdmissao(rs.getObject("dtAdmissao", LocalDate.class));
        return registro;
    }
}
