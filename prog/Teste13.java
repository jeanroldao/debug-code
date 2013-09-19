import java.util.*;
import java.io.*;

public class Teste13 {
	
	static {
		System.loadLibrary("Teste13native");
	}
	
	private String nome;
	
	public void setNome(String nome) {
		this.nome = nome;
	}
	
	public String getNome() {
		return nome;
	}
	
	public native void falar();
	
	public native String getFrase();
	
	public static void main(String[] args) throws Exception {
		Teste13 t = new Teste13();
		t.setNome("jean");
		t.falar();
	}
	
}

