import java.util.*;
import java.io.*;

public class Teste9p {
	
	private String nome;
	
	public Teste9p(String nome) {
		this.nome = "-" + nome + "-";
	}
	
	public String getNome() {
		return nome.replace("-", "");
	}
}

