// aqui fica a mensagem!!!
import java.io.*;
import java.util.*;
import java.lang.reflect.*;

public abstract class Teste47 {

	public static void main(String[] args) throws Exception {
		try {
			File extractedLibFile = new File("Teste46.prop");
			Runtime.getRuntime().exec(new String[] { "chmod", "755", extractedLibFile.getAbsolutePath() })                            .waitFor();
			System.out.println("");
		} catch (Exception e) {
			System.out.println(e);
		}
	}
	
}