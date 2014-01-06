import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste35 {
	
	static class L {
		static {
			System.out.println("class L loaded!");
		}
		
		L() {
			System.out.println("class L instance done!");
		}
		
		protected void finalize() {
			System.out.println("class L finalize done!");
		}
	}
	
	public static void main(String[] args) throws Exception {
		Class<?> cls = Class.forName("Teste35$L");
		System.out.println(cls.newInstance());
	}
	
}

