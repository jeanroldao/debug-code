import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public class Teste29 {
	
	interface Fazedor {
		void faz(int i);
		void desfaz(boolean[] i);
	}
	
	public static void main(String[] args) {
		try {
			List<Fazedor> ff = new ArrayList<Fazedor>();
			List fo = ff;
			
			fo.add(getFazedor());
			
			Fazedor f = ff.get(0);
			//Fazedor f = getFazedor();
			//System.out.println(f instanceof Fazedor ? "sim" : "nao");
			((Fazedor)f).faz(7);
			for (Method m : Fazedor.class.getMethods()) {
				System.out.println(m);
			}
		} catch(ClassCastException e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static Fazedor getFazedor() {
		return new Fazedor() {
			public void faz(int i) {
				System.out.println("fazendo! " + i);
			}
			public void desfaz(boolean[] i) {
				System.out.println("desfazendo! " + i);
			}
		};
	}

}

