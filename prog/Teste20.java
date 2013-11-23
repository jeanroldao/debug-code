import java.util.*;
import java.io.*;
import java.lang.ref.*;

public class Teste20 {
	
	static int seq = 0;
	
	int id = 0;
	
	public Teste20() {
		id = seq++;
		System.out.println("start... ("+id+")");
	}
	
	public int getId() {
		return id;
	}
	
	protected void finalize() {
		System.out.println("...end ("+id+")");
	}

	static Reference<Teste20> ref;
	
	static Teste20 get() {
		if (ref == null || ref.get() == null) {
			ref = new WeakReference<Teste20>(new Teste20());
		}
		return ref.get();
	}
	
	public static void main(String[] args) throws Exception {
		Scanner scan = new Scanner(System.in);
		
		while (true) {
			System.out.println(get().getId());
			if (scan.nextLine().equals("gc")) {
				System.gc();
			}
		}
	}
}

