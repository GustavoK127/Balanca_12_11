package Dao;

import Model.ObrasEngenheiros;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

public class ObrasEngenheirosDao {
    private static final String SELECT = """
            SELECT v.idObra, v.idEngenheiro, v.dtAdmissaoEngenheiro, v.dtInicioTrabalho, v.dtFimTrabalho, v.idFuncao,
                   e.dsNome AS nomeEngenheiro, f.dsFuncao AS descricaoFuncao
            FROM obras_engenheiros v
            LEFT JOIN engenheiros e ON e.idEngenheiro = v.idEngenheiro
            LEFT JOIN funcoes f ON f.idFuncao = v.idFuncao
            """;

    public void inserir(ObrasEngenheiros registro) throws SQLException {
        Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "obras", "idObra", registro.idObra());
            Sql.exigirRegistro(conexao, "engenheiros", "idEngenheiro", registro.idEngenheiro());
            Sql.exigirRegistro(conexao, "funcoes", "idFuncao", registro.idFuncao());
            Sql.executar(conexao, """
                    INSERT INTO obras_engenheiros (idObra, idEngenheiro, dtAdmissaoEngenheiro, dtInicioTrabalho, dtFimTrabalho, idFuncao)
                    VALUES (?, ?, ?, ?, ?, ?)
                    """, registro.idObra(), registro.idEngenheiro(),
                    registro.dtAdmissaoEngenheiro(), registro.dtInicioTrabalho(), registro.dtFimTrabalho(), registro.idFuncao());
            return null;
        });
    }

    public boolean alterar(ObrasEngenheiros registro) throws SQLException {
        return Sql.emTransacao(conexao -> {
            Sql.exigirRegistro(conexao, "funcoes", "idFuncao", registro.idFuncao());
            return Sql.executar(conexao, """
                    UPDATE obras_engenheiros SET dtAdmissaoEngenheiro = ?, dtInicioTrabalho = ?, dtFimTrabalho = ?, idFuncao = ?
                    WHERE idObra = ? AND idEngenheiro = ?
                    """, registro.dtAdmissaoEngenheiro(), registro.dtInicioTrabalho(), registro.dtFimTrabalho(),
                    registro.idFuncao(), registro.idObra(), registro.idEngenheiro()) > 0;
        });
    }

    public boolean excluir(int idObra, int idEngenheiro) throws SQLException {
        return Sql.executar("DELETE FROM obras_engenheiros WHERE idObra = ? AND idEngenheiro = ?",
                idObra, idEngenheiro) > 0;
    }

    public Optional<ObrasEngenheiros> buscar(int idObra, int idEngenheiro) throws SQLException {
        return Sql.buscar(SELECT + " WHERE v.idObra = ? AND v.idEngenheiro = ?",
                ObrasEngenheirosDao::ler, idObra, idEngenheiro);
    }

    public List<ObrasEngenheiros> listar(int idObra) throws SQLException {
        return Sql.listar(SELECT + " WHERE v.idObra = ? ORDER BY v.idEngenheiro", ObrasEngenheirosDao::ler, idObra);
    }

    private static ObrasEngenheiros ler(ResultSet rs) throws SQLException {
        return new ObrasEngenheiros(rs.getInt("idObra"), rs.getInt("idEngenheiro"),
                rs.getObject("dtAdmissaoEngenheiro", LocalDate.class),
                rs.getObject("dtInicioTrabalho", LocalDate.class),
                rs.getObject("dtFimTrabalho", LocalDate.class),
                rs.getInt("idFuncao"),
                rs.getString("nomeEngenheiro"), rs.getString("descricaoFuncao"));
    }
}
