import java.util.*;
import java.io.*;

public class Teste21 {
	
	static class Pessoa {
		private String nome;
		
		public Pessoa(String nome) {
			this.nome = nome;
		}
		
		public String getNome() {
			return nome;
		}
	}
	
	public static void main(String[] args) throws Exception {
		
		//Map<String, Pessoa> p = new HashMap<String, Pessoa>((int) (15000 / 0.75) + 1);
		Map<String, Pessoa> p = new HashMap<String, Pessoa>();
		
		for (int i = 0; i < 12; i++) {
			System.out.println(i);
			p.put("id_"+i, new Pessoa("("+i+")"));
		}
		System.out.println("searching...");
		System.out.println("done: " + p.get("id_3").getNome());
		new Scanner(System.in).nextLine();
	}
	
	public static void main0(String[] args) throws Exception {
		//System.out.println(new String("qualquer coisa") == new String("qualquer coisa"));
		
		List<String> names = new ArrayList<String>();
		//String[] names = new String[1000000];
		
		for (int i = 0; i < 15; i++) {
			names.add(String.valueOf(i*i));
		}
		
		System.out.println("done: " + names);
		//new Scanner(System.in).nextLine();
	}
}

