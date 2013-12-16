import java.util.*;
import java.io.*;

public class Teste13 {
	
	static {
		String path = "";
		try {
			path = new File("Teste13native.dll").getCanonicalPath().toString();
			System.out.println(path);
		} catch (Exception e) {
			System.out.println(e);
		}
		//System.loadLibrary("Teste13native");
		System.load(path);
	}
	
	private String nome;
	
	public void setNome(String nome) {
		this.nome = nome;
	}
	
	public String getNome() {
		return nome;
	}
	
	public native void falar(String prop);
	
	public static void main(String[] args) throws Exception {
		Teste13 t = new Teste13();
		t.setNome("twenty");
		t.falar("age");
	}
	
}

