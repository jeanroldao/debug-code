import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste38 {

	public static void main(String[] args) throws Exception {
		try {
			Method m = Teste38_T2.class.getMethod("nd");
			System.out.println(m);
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
	
}

class Teste38_T1 {
	private void nd() {}
}

class Teste38_T2 extends Teste38_T1 {
	
}
