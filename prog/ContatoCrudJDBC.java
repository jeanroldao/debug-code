import java.sql.Connection;
import java.sql.Date;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

public class ContatoCrudJDBC {
	
	public void salvar(Contato contato) {
		Connection conexao = geraConexao();
		PreparedStatement insereSt = null;
		
		String sql = "insert into contato(nome, telefone, email, dt_cad, obs) "
					+"values (?, ?, ?, ?, ?)";
		
		try {
			//System.out.println("insert into: " + conexao);
			insereSt = conexao.prepareStatement(sql);
			insereSt.setString(1, contato.getNome());
			insereSt.setString(2, contato.getTelefone());
			insereSt.setString(3, contato.getEmail());
			insereSt.setLong(4, contato.getDataCadastro());
			insereSt.setString(5, contato.getObservacao());
			insereSt.executeUpdate();
		} catch (SQLException e) {
			System.out.println("Erro ao incluir contato: " + e.getMessage());
		} finally {
			try {
				insereSt.close();
				//conexao.close();
			} catch (SQLException e) {
				System.out.println("Erro ao fechar operações de inserção: " + e.getMessage());
			}
		}
	}
	
	public void atualizar(Contato contato) {
		Connection conexao = geraConexao();
		PreparedStatement atualizaSt = null;
		
		String sql = "update contato set nome=?, telefone=?, email=?, dt_cad=?, obs=? where codigo=?";
		
		try {
			
			atualizaSt = conexao.prepareStatement(sql);
			atualizaSt.setString(1, contato.getNome());
			atualizaSt.setString(2, contato.getTelefone());
			atualizaSt.setString(3, contato.getEmail());
			atualizaSt.setLong(4, contato.getDataCadastro());
			atualizaSt.setString(5, contato.getObservacao());
			atualizaSt.setInt(6, contato.getCodigo());
			atualizaSt.executeUpdate();
		} catch (SQLException e) {
			System.out.println("Erro ao atualizar contato: " + e.getMessage());
		} finally {
			try {
				atualizaSt.close();
				//conexao.close();
			} catch (SQLException e) {
				System.out.println("Erro ao fechar operações de atualização: " + e.getMessage());
			}
		}
	}
	
	public void excluir(Contato contato) {
		Connection conexao = geraConexao();
		PreparedStatement deletaSt = null;
		
		String sql = "delete from contato where codigo=?";
		
		try {
			
			deletaSt = conexao.prepareStatement(sql);
			deletaSt.setInt(1, contato.getCodigo());
			deletaSt.executeUpdate();
		} catch (SQLException e) {
			System.out.println("Erro ao excluir contato: " + e.getMessage());
		} finally {
			try {
				deletaSt.close();
				//conexao.close();
			} catch (SQLException e) {
				System.out.println("Erro ao fechar operações de exclusão: " + e.getMessage());
			}
		}
	}
	
	public List<Contato> listar() {
		Connection conexao = geraConexao();
		List<Contato> contatos = new ArrayList<Contato>();
		Statement consulta = null;
		ResultSet resultado = null;
		
		String sql = "select * from contato";
		
		try {
			consulta = conexao.createStatement();
			resultado = consulta.executeQuery(sql);
			
			while (resultado.next()) {
				Contato contato = new Contato();
				
				contato.setCodigo(resultado.getInt("codigo"));
				contato.setNome(resultado.getString("nome"));
				contato.setTelefone(resultado.getString("telefone"));
				contato.setEmail(resultado.getString("email"));
				contato.setDataCadastro(resultado.getLong("dt_cad"));
				contato.setObservacao(resultado.getString("obs"));
				
				contatos.add(contato);
			}
			
		} catch (SQLException e) {
			System.out.println("Erro ao listar contatos: " + e.getMessage());
		} finally {
			try {
				consulta.close();
				resultado.close();
				//conexao.close();
			} catch (SQLException e) {
				System.out.println("Erro ao fechar operações de listar: " + e.getMessage());
			}
		}
		return contatos;
	}
	
	public Contato buscaContato(int codigo) {
		Connection conexao = geraConexao();
		PreparedStatement buscaSt = null;
		
		ResultSet resultado = null;
		
		Contato contato = null;
		
		String sql = "select * from contato where codigo=?";
		
		try {
			
			buscaSt = conexao.prepareStatement(sql);
			buscaSt.setInt(1, codigo);
			resultado = buscaSt.executeQuery();
			if (resultado.next()) {
				contato = new Contato();
				
				contato.setCodigo(resultado.getInt("codigo"));
				contato.setNome(resultado.getString("nome"));
				contato.setTelefone(resultado.getString("telefone"));
				contato.setEmail(resultado.getString("email"));
				contato.setDataCadastro(resultado.getLong("dt_cad"));
				contato.setObservacao(resultado.getString("obs"));
				
			}
		} catch (SQLException e) {
			System.out.println("Erro ao buscar contato: " + e.getMessage());
		} finally {
			try {
				buscaSt.close();
				resultado.close();
				//conexao.close();
			} catch (SQLException e) {
				System.out.println("Erro ao fechar operações de buscar: " + e.getMessage());
			}
		}
		return contato;
	}
	
	private Connection conexao;
	public Connection geraConexao() {
		
		if (conexao != null) {
			return conexao;
		}
		
		try {
			/*
			String url = "jdbc:mysql://localhost/agenda";
			String usuario = "root";
			String senha = "root";
			
			conexao = DriverManager.getConnection(url, usuario, senha);
			//*/
			
			/*
			Class.forName("smallsql.database.SSDriver");
			String db;
			if (System.getProperty("PHP_VERSION") == null) {
				db = "emp1";
			} else {
				db = "emp1";
			}
			conexao = DriverManager.getConnection( "jdbc:smallsql:"+db );

			//*/
			
			//*/
			Class.forName("org.sqlite.JDBC");
			conexao = DriverManager.getConnection("jdbc:sqlite:C:\\java-web\\contato.db");
			//*/
		} catch (Exception e) {
			System.out.println("Erro ao conectar ao banco de dados. ERRO: " + e.getMessage());
		}
		return conexao;
	}
	
	public static void main(String[] args) {
		ContatoCrudJDBC contatoDao = new ContatoCrudJDBC();
		
		String os_name = System.getProperty("os.name");
		
		Contato beltrano = new Contato();
		beltrano.setNome(os_name +" beltrano");
		beltrano.setTelefone("(00) 1234567");
		beltrano.setEmail("beltrano@teste.com.br");
		beltrano.setDataCadastro(System.currentTimeMillis());
		beltrano.setObservacao("Novo cliente");
		System.out.println("saving " + beltrano);
		contatoDao.salvar(beltrano);
		System.out.println("ok");
		
		Contato fulano = new Contato();
		fulano.setNome(os_name +" fulano");
		fulano.setTelefone("(00) 312321312");
		fulano.setEmail("fulano@teste.com.br");
		fulano.setDataCadastro(System.currentTimeMillis());
		fulano.setObservacao("Novo contato");
		System.out.println("saving " + fulano);
		contatoDao.salvar(fulano);
		System.out.println("ok");
		
		List<Contato> contatos = contatoDao.listar();
		System.out.println("Contatos cadastrados: " + contatos.size());
		for (Contato c : contatos) {
			System.out.println(c);
			//c = contatoDao.buscaContato(c.getCodigo());
			//System.out.println(c);
		}
		
		/*
		if (contatos.size() > 3) {
			for (int i = 0; i < 3; i++) {
				Contato c = contatos.get(i);
				System.out.println("deleting " + c);
				contatoDao.excluir(c);
				System.out.println("ok");
			}
		}
		//*/
	}

}
