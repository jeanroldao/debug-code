import java.util.*;
import java.io.*;

public class Teste {

	public static void main(String[] args) throws Exception {
	/*
	  System.out.println("oi");
	  long len = 600851475143L;
	  for (String s : args) {
		System.out.println(s);
	  }
	  System.out.println(len);
	  */
	  //System.out.println(Teste.class.toString());
	  //System.out.println(new String(new char[]{'e', 'u', ' ', 's', 'o', 'u', ' ', 'j', 'e', 'a', 'n'}).hashCode());
	  //char[] c = "jean".toCharArray();
	  //System.out.println(c);
	  /*
	  System.out.print("\n");
	  for (int i = 0; i <= 666; i++) {
		int k = 0;
		for (; k < i*10000; k++) {}
		System.out.print(k);
		System.out.print("\n");
	  }
	  System.out.println("fim do mundo? D: ");
	  */
		/*
		List<String> l = new ArrayList<String>();
		for (long i = 1L; i <= 999999999999999L; i*=10L) {
			l.add(String.valueOf(i));
		}
		
		int lastLength = 0;
		for (String s : l) {
			if (lastLength != s.length()) {
				lastLength = s.length();
				System.out.println(s);
			}
		}
		*/
		
		/*
		System.out.println(Teste1.Debug);
		System.out.println(Teste1.getDebug());
		if (Teste1.getDebug()) {
			System.out.println("SIM");
		} else {
			System.out.println("NOO");
		}

		
		Serializable s = null;
		final String snome = "Jean" + "L";
		final String ssobrenome = "Roldão";
		
		for (int i = 0; i < 3; i++) {
			final int fi = i;
			s = new Serializable() {
				
				private String nome;
				private String sobrenome;
				
				public Serializable setNome() {
					nome = snome;
					sobrenome = ssobrenome;
					System.out.println(nome);
					System.out.println(sobrenome);
					System.out.println(fi);
					return this;
				}
			}.setNome();
		}
		
		// Write to disk with FileOutputStream
		FileOutputStream f_out = new 
			FileOutputStream("myobject.data");

		// Write object with ObjectOutputStream
		ObjectOutputStream obj_out = new
			ObjectOutputStream (f_out);
		
		// Write object out to disk
		obj_out.writeObject ( s );
		*/
		
		/*
		System.out.println(1);
		Thread.sleep(1000);
		System.out.println(2);
		*/
		
		Manager m = new Manager();
		m.addCall(new Delegate() {
			public void call() {
				System.out.println(Position.PRIMEIRO + this.getClass().getName());
			}
		});
		
		m.addCall(new Delegate() {
			public void call() {
				System.out.println(Position.SEGUNDO + this.getClass().getName());
			}
		});
		
		m.addCall(new Delegate() {
			public void call() {
				System.out.println(Position.TERCEIRO + this.getClass().getName());
			}
		});

		m.addCall(new Delegate() {
			public void call() {
				System.out.println(Position.valueOf("PRIMEIRO") + this.getClass().getName());
			}
		});
		
		m.exec();
	}
	
}

enum Position {
	PRIMEIRO() {
		public String toString() {
			return "1o";
		}
	}, SEGUNDO() {
		public String toString() {
			return "2o";
		}
	}, TERCEIRO() {
		public String toString() {
			return "3o";
		}
	}
};

interface Delegate {
	void call();
}
class Manager {
	List<Delegate> calls = new ArrayList<Delegate>();
	
	public void addCall(Delegate call) {
		calls.add(call);
	}
	
	public void exec() {
		for (Delegate d : calls) {
			//try {Thread.sleep(1000);} catch (Exception e) {}
			d.call();
		}
	}
}