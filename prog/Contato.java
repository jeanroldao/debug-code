import java.sql.Date;

public class Contato {
	private Integer codigo;
	private String nome;
	private String telefone;
	private String email;
	private Long dataCadastro;
	private String observacao;
	
	public Integer getCodigo() {
		return codigo;
	}
	public void setCodigo(Integer codigo) {
		this.codigo = codigo;
	}
	public String getNome() {
		return nome;
	}
	public void setNome(String nome) {
		this.nome = nome;
	}
	public String getTelefone() {
		return telefone;
	}
	public void setTelefone(String telefone) {
		this.telefone = telefone;
	}
	public String getEmail() {
		return email;
	}
	public void setEmail(String email) {
		this.email = email;
	}
	public Long getDataCadastro() {
		return dataCadastro;
	}
	public void setDataCadastro(Long dataCadastro) {
		this.dataCadastro = dataCadastro;
	}
	public String getObservacao() {
		return observacao;
	}
	public void setObservacao(String observacao) {
		this.observacao = observacao;
	}
	
	public String toString() {
		return super.toString() + " {codigo="+codigo+", nome="+nome+"}";
	}
}
